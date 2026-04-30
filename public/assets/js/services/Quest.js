app.factory('Quest', function($http, $httpParamSerializerJQLike) {
    
    return {
        getApi: function(file, data) {
            return $http.get(file + "?" + $httpParamSerializerJQLike(data));
        },

        postApi: function(file, data) {
            
            var x = Math.random().toString(36).substring(2, 15);
            // skip encryption, try ko next time

            return $http({
                method: 'POST',
                url: file,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X': x
                },
                data: $httpParamSerializerJQLike(data)
            });
        },
        // for uploading files/images
        postFormData: function(file, formData) {
            var x = Math.random().toString(36).substring(2, 15);

            return $http({
                method: 'POST',
                url: file,
                headers: {
                    'Content-Type':undefined,
                    'X': x
                },
                transformRequest: angular.identity,
                data: formData
            });
        }
    };
});