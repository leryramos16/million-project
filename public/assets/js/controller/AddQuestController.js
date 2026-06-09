app.controller('AddQuestController', function($scope, Quest) {

    $scope.quest = {
        title: '',
        description: ''
    };

    $scope.customModal = function(options) {
        return Swal.fire({
            title: options.title || '',
            text: options.text || '',
            icon: options.icon || 'info',
            showCancelButton: options.showCancelButton || false,
            confirmButtonText: options.confirmButtonText || 'OK',
            cancelButtonText: options.cancelButtonText || 'Cancel',

            customClass: {
                popup: 'medieval-popup',
                title: 'medieval-title',
                htmlContainer: 'medieval-text',
                confirmButton: 'medieval-confirm',
                cancelButton: 'medieval-cancel'
            },
            buttonsStyling: false
        });
    };

    $scope.submitQuest = function() {
        console.log("paymentProof:", $scope.quest.paymentProof);
        if (!$scope.quest.title || !$scope.quest.description) {
            $scope.customModal({
                title: 'Incomplete details mate!',
                text: 'I need your Quest.',
                icon: 'warning'
            });
            return;
        }

        if (!$scope.quest.paymentProof) {
            $scope.customModal({
                title: 'No Payment',
                text: 'Please upload payment screenshot.',
                icon: 'warning'
            });
            return;
        }

        var formData = new FormData();

        formData.append('method', 'addQuest');
        formData.append('title', $scope.quest.title);
        formData.append('description', $scope.quest.description);
        formData.append('payment_proof', $scope.quest.paymentProof);

        Quest.postFormData('/mymillionpesoproject/public/questapi', formData)
            .then(function(response) {
                console.log("This is the response.data", response.data);

                if (response.data.status) {
                    Swal.fire("Success", response.data.message, "success")
                        .then(function() {
                             window.location.href = '/mymillionpesoproject/public/mainpage';
                        });
                } else {
                    Swal.fire("Failed", response.data.message, "warning");
                }
            })
            .catch(function(error){
                console.error(error);
                Swal.fire("Error", "Failed to submit quest", "error");
            });
    };
});