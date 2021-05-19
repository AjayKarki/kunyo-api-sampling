module.exports = {
    getCsrfToken: function () {
        return $('meta[name="csrf-token"]').attr('content');
    },

    getAssetPath: function () {
        return $('meta[name="asset-path"]').attr('content') + '/';
    },

    http: {
        get: function (url , data) {
            return this.ajax('GET', url, data);
        },

        post: function (url , data) {
            return this.ajax('POST', url, { ...{ _token: $('meta[name="csrf-token"]').attr('content') }, ...data });
        },

        ajax: function (type, url, data) {
            return $.ajax({
                type: type,
                url: url,
                data: data
            });
        }
    },

    toast: function (message, type) {
        var typeOptions = ['warning', 'success', 'error', 'info',]
        type = type || 'info'
        if (typeOptions.includes(type)) {
            toastr[type](message, '', {timer: 5000});
        } else {
            console.error("`" + type + "` is not available type for toast! Use the below type values : ");
            console.table(typeOptions);
        }
    },

    getImagePath: function (path) {
        return this.getAssetPath() + 'img/' + path;
    },

    fileExists: function (url) {
        var http = new XMLHttpRequest();

        http.open('HEAD', url, false);
        http.send();

        return http.status !== 404;
    },

    reload: function (timer) {
        setTimeout(function () {
            location.reload();
        }, timer || 3000);
    },

    location: function (path) {
        location.href = path;
    },

    merge: function () {
        // Create a new object
        var extended = {};

        // Merge the object into the extended object
        var merge = function (obj) {
            for (var prop in obj) {
                if (obj.hasOwnProperty(prop)) {
                    // Push each value from `obj` into `extended`
                    extended[prop] = obj[prop];
                }
            }
        };

        // Loop through each object and conduct a merge
        for (var i = 0; i < arguments.length; i++) {
            merge(arguments[i]);
        }

        return extended;
    },

    sluggify: function (text) {
        return text.toLowerCase()
            .replace(/ /g,'-')
            .replace(/[-]+/g, '-')
            .replace(/[^\w-]+/g,'');
    },

    /**
     * Use this at the last after datatable is drawn
     * utils.hideDataTableBulkAction(0);
     * @param columns
     */
    hideDataTableBulkAction : function (columns) {
        var collectionButton = document.getElementsByClassName('datatable-buttons-bulk-action');

        for (var i = 0; i < collectionButton.length; i ++) {
            collectionButton[i].style.display = 'none';
        }

        if (typeof dataTable !== 'undefined' && typeof columns !== 'undefined') {
            dataTable.column( columns ).visible(false)
        }
    }

};
