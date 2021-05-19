module.exports = {
    routes: {
    "admin.payment-region.show": "/spanel/payment-region/{payment_region}",
    "admin.payment-region.edit": "/spanel/payment-region/{payment_region}/edit",
    "admin.payment-region.update": "/spanel/payment-region/{payment_region}",
    "admin.payment-region.destroy": "/spanel/payment-region/{payment_region}",
    "manager.render.assigned-orders": "/spanel/manager/assigned/orders"
},
    get: function (query, params) {
        params = params || {}
        if (!this.routes.hasOwnProperty(query)) {
            console.error("`" + query + "` Route Not Found!");
            return;
        }
        var routeFound = this.routes[query];
        if (Object.keys(params).length !== 0) {
            var routeParams = routeFound.match(/[^{}]+(?=})/g)
            if (routeParams.length) {
                routeParams.forEach(function (param) {
                    routeFound = routeFound.replace('{'+param+'}', params[param] || '{'+param+'}')
                })
                return routeFound;
            }
        }
        return routeFound;
    }
};
