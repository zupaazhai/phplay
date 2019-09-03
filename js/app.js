var App = function () {

    this.el = {
        fileList: document.getElementById('file-list'),
        fileItem: document.querySelectorAll('.file-item'),
        outputIframe: document.getElementById('output-iframe'),
        editor: document.getElementById('codearea'),
        newFileBtn: document.getElementById('new-file-btn'),
        columns: document.getElementById('columns'),
        iframeOverlay: document.getElementById('iframe-overlay')
    }

    this.editor = null
    this.currentFile = {}
    this.phpOpenTag = "<" + '?php' + " \n"
}

App.prototype = {
    
    constructor: App,

    run: function () {

        this.setEditor()
        this.getFiles()
        this.el.newFileBtn.addEventListener('click', function () {
            this.create(this.phpOpenTag)
        }.bind(this))
        this.bindResizeColumn()
    },

    bindResizeColumn: function () {
        var self = this
        resizableGrid(this.el.columns, function () {
            self.toggleIframeOverlay().show()
        }, function () {
            self.toggleIframeOverlay().hide()
        })
    },

    toggleIframeOverlay: function () {

        var self = this

        return {
            show: function () {
                self.el.iframeOverlay.classList.add('active')
            },
            hide: function () {
                self.el.iframeOverlay.classList.remove('active')
            }
        }
    },

    setEditor: function () {
        var self = this

        this.editor = CodeMirror.fromTextArea(this.el.editor, {
            lineNumbers: true,
            styleActiveLine: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            extraKeys: {
                "Ctrl-S": function () {
                    self.onSave()
                },
                'Ctrl-Enter': function () {
                    self.onSave()
                }
            }
        })
        
        this.editor.setSize("100%", "100%")
        this.editor.setOption("theme", 'mdn-like')
        this.editor.getDoc().setValue(this.phpOpenTag);
    },

    onSave: function () {

        var self = this

        if (!this.currentFile.name) {
            return self.create()
        }

        this.xhr({
            method: 'post',
            url: window.url.updateFile,
            data: {
                name: this.currentFile.name,
                content: this.editor.getValue()
            },
            success: function () {
                self.open(self.currentFile, false)
            },
            fail: function () {

            }
        })

        console.log(this.currentFile)
    },

    checkFilename: function (name) {
        var reg = /^[^\\/:\*\?"<>\|]+$/,
            reg2 = /^\./,
            reg3 = /^(nul|prn|con|lpt[0-9]|com[0-9])(\.|$)/i
    
        return reg.test(name) && !reg2.test(name) && !reg3.test(name)
    },

    open: function (file, setNewValue) {

        this.currentFile = file
       
        var self = this,
            src = file.url + '?rand=' + (new Date().getTime())
        
        this.el.outputIframe.src = src
        this.xhr({
            method: 'get',
            url: window.url.getFile + file.name,
            success: function (res) {
                res = JSON.parse(res)
                var content = res.data.content
                
                if (setNewValue) {
                    self.editor.getDoc().setValue(content)
                }
            }
        })
    },

    create: function (content) {

        var self = this,
            name = prompt('Enter file name. ex script.php')

        content = content || this.editor.getValue()

        if (!name.length) {
            return
        }

        if (!this.checkFilename(name)) {
            alert('File name is in valid')
        }

        this.xhr({
            method: 'post',
            url: window.url.createFile,
            data: {
                name: name,
                content: content
            },
            success: function (res) {
                self.getFiles()
                res = JSON.parse(res)

                var newFile = res.data

                self.open(newFile)
            },
            fail: function (res) {
                res = JSON.parse(res)

                if (res.msg == 'file_exists') {
                    alert('File already existsl')
                }
            }
        })
    },

    getFiles: function () {
        var self = this

        self.el.fileList.innerHTML = ''
        
        this.xhr({
            method: 'get',
            url: window.url.getFiles,
            success: function (res) {
                res = JSON.parse(res)
                var files  = res.data

                files.forEach(function (file) {
                    var li = document.createElement('li'),
                        textSpan = document.createElement('span'),
                        closeSpan = document.createElement('span')
                    
                    closeSpan.innerHTML = '&times;'
                    textSpan.innerText = file.name
                    
                    li.appendChild(textSpan)
                    li.appendChild(closeSpan)

                    closeSpan.addEventListener('click', function () {
                        var result = confirm('Are you sure to delete ' + file.name)

                        if (!result) {
                            return
                        }

                        self.deleteFile(file)
                    })

                    textSpan.addEventListener('click', function () {
                        self.open(file, true)
                    })
                    self.el.fileList.appendChild(li)
                })
            },
            fail: function () {

            }
        })
    },

    deleteFile: function (file) {

        var self = this

        this.xhr({
            method: 'post',
            url: window.url.deleteFile,
            data: {
                name: file.name
            },
            success: function (res) {
                self.el.outputIframe.remove()
                window.location.reload()
            },
            fail: function () {

            }
        })
    },

    xhr: function (opts) {

        var req = new XMLHttpRequest
        
        opts.data = opts.data || {}
        opts.url = opts.url || '/'
        req.open(opts.method, opts.url, true)

        if (opts.method == 'post') {
            req.setRequestHeader('Content-Type', 'application/json');
        }

        req.onload = function () {
            if (this.status >= 200 && this.status < 400) {
                var res = this.response
                if (typeof opts.success == 'function') {
                    opts.success(res)
                }
            } else {
                var res = this.response
                if (typeof opts.fail == 'function') {
                    opts.fail(res)
                }    
            }
        }

        req.send(JSON.stringify(opts.data))
    }
}

window.onload = function () {
    var app = new App
    app.run()
}
