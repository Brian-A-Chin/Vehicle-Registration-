$(window).on("load", function () {
    const AdminMode = $('.new-vehicle-make').length == 0;
    var timeout;
    var insertingNew = false;
    var quickAction = false;
    var TotalVehicles = 0;
    var vehicleDatabase = {
        make: [],
        alt_make: [],
        model: [],
        color: ['Beige', 'Black', 'Blue', 'Brown', 'Gold', 'Grey', 'Green', '', 'Orange', 'Pink', 'Purple', 'Red', 'Silver', 'White', 'Yellow'],
        images: [],
        state:[]
    }

    var savedVehicles = {};
    var selectedVehicleId;

    var newVehicle = {
        make:false,
        model:false,
        color:false,
        plate:false,
        state:false,
        year:false,
    }
    

    var jsonURLs = {
        make:'datasets/makes.json',
        images:'datasets/images.json',
        model:'https://vpic.nhtsa.dot.gov/api/vehicles/GetModelsForMake/_THIS_?format=json',
        color: false,
        states: 'datasets/states.json',
    }
    

    var jsonKeys = {
        model:'Model_Name',
        color:'color_name'
    }
    
    function showSideMenu() {
        $loader.fadeOut();
        $('.side-container,.overlay').fadeIn();
    }
    
    function hideSideMenu(){
        $('.side-container,.overlay').fadeOut();
    }
    
    if (!$('.loader').length) {
        $('body').prepend('<div class="loader"></div><div class="overlay"></div>');
        $loader = $('.loader');
        $overlay = $('.overlay');

    }

    function generateKey(max) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < max; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    function validateJSON(response){
       if(typeof response =='object'){
           return true;
       }else{
           return false;
       }
    }

    function highlightMissingFields(params) {
        var errorCount = 0;
        $('#' + params.parent + ' .' + params.targetClass).each(function () {
            var min = parseInt(this.hasAttribute('min') ? $(this).attr('min') : 1);
            var max = parseInt(this.hasAttribute('max') ? $(this).attr('max') : 256);
            if ((this.value.length < min || this.value.length > max) || ($(this).prop("tagName") === 'SELECT' && this.value === 'false')) { 
                errorCount += 1;
                $(this).css('border', '1px solid red');
                $(this).next('small').remove();
                if (this.value.length < min && min > 1) {
                    $(this).after('<small class="text-danger">Min (' + min + ') Characters </small>')
                } else if (this.value.length > max) {
                    $(this).after('<small class="text-danger">Max (' + max + ') Characters </small>')
                }
            } else {
                $(this).removeAttr('style');
                $(this).next('small').remove();
            }
        });
        if (errorCount > 0)
            toastr.error('Please correct highlighted field(s)')
        return errorCount > 0
    }

    function showDialog(params) {
        $overlay.fadeIn();
        $('.main-dialog').fadeIn("slow", function () {
            $loader.fadeOut(500);
        });
    }

    

    function hideDialog(params) {
        $overlay.fadeOut();
        $('.main-dialog').fadeOut(600);
        $("#dialog-confirm").show();
    }

    function mapVehicles() {
        TotalVehicles = 0;
        $('.stored-vehicle').each(function () {
            TotalVehicles += 1;
            if (!AdminMode) {
                if (!this.hasAttribute('id')) {
                    var key = generateKey(7);
                    var make = $(this).attr('data-make');
                    savedVehicles[key] = {
                        CarID: $(this).attr('data-ID'),
                        make: make,
                        model: $(this).attr('data-model'),
                        color: $(this).attr('data-color'),
                        plate: $(this).attr('data-plate'),
                        state: $(this).attr('data-state'),
                        year: $(this).attr('data-year')
                    }
                    $(this).attr('id', key);
                    $(this).find('.remove-vehicle').attr('data-RID',key);
                    selectedVehicleId = key;
                    setMakeImage(make);
                }
            } else {
                var $this = $(this).find('.stored-vehicle-image');
                $this.attr('src', setMakeImage($this.attr('data-make'),true));
            }
        });
        if (!AdminMode) {
            if (TotalVehicles > 0) {
                $('#help-intro').text('Click on a vehicle logo below to edit.');
            } else {
                $('#help-intro').text('No vehicles found. Please Register a vehicle using the form above.');
            }
        }
        $('#totalVehicles').text(TotalVehicles);
    }

    function loadAllMakes() {
        if (vehicleDatabase.alt_make.length != 0) {
            $.merge(vehicleDatabase.make, vehicleDatabase.alt_make);
            vehicleDatabase['alt_make'] = [];
            setInput('make');
        }
    }
   
    function updateNewVehicleProgress(){
        var setter;
        $.each(newVehicle,function(key,value){
           
            if(!value){
                if(jsonURLs.hasOwnProperty(key)){
                    var url = jsonURLs[key] == false ? jsonURLs[key] : jsonURLs[key].replace('_THIS_',setter);
                    getNextOptionSet(url,key);
                }
                return false;
            }else{
                 setter = value;
            }
        });
    }
    
    function clearNewVehicle(){
        $.each(newVehicle,function(key,value){
              newVehicle[key] = false; 
        });
    }

    function checkInputs() {
        if ($('.selected-vehicle-make').val().length == 0) {
            $('#editVehicleForm .vehicle-model').prop('disabled', 'disabled').val("");
            
        }
        if (!AdminMode){
            if ($('.new-vehicle-make').val().length == 0) {
                $('#newVehicleForm .vehicle-model').prop('disabled', 'disabled').val("");
            }
        }
    }
    
    function NewVehicleIsFilled(){
        var i = 0;
        $.each(newVehicle,function(key,value){
              if(value != false)
                  i++;
        });
        return i == Object.keys(newVehicle).length;
    }


    
    function setInput(target){
        var $elem = $(".vehicle-" + target);
        $elem.removeAttr('disabled');
        $elem.removeClass('disabled');
        $elem.autocomplete({
            source: vehicleDatabase[target],
            minlength: 2,
            change: function (event, ui) {
                var propertyName = $(this).attr('placeholder').toLowerCase();
                if (propertyName === 'color')
                    newVehicle[propertyName] = event.currentTarget.value;
                if (ui.item == null && propertyName !== 'color') {
                    event.currentTarget.value = ''; 
                    event.currentTarget.focus();
                }
            },
            select: function(event, ui) {
                var propertyName = $(this).attr('placeholder').toLowerCase();
                if ($(this).hasClass('dependent')) {
                    if (savedVehicles.hasOwnProperty(selectedVehicleId)) {
                        if (savedVehicles[selectedVehicleId].hasOwnProperty(propertyName)) {
                            if (savedVehicles[selectedVehicleId][propertyName] !== ui.item.value) {
                                $("input[type=text]").not(".dependent").val("");
                                clearNewVehicle();
                            }
                        }
                    }
                }
                if (newVehicle.hasOwnProperty(propertyName)) {
                    newVehicle[propertyName] = ui.item.value;
                }
                switch (propertyName) {
                    case 'model':
                        $('#invalidModel').remove();
                        $('#model').removeAttr('style');
                        break;
                }
                updateNewVehicleProgress();
            }
        });
    }

    
    function getJson(url) {
        var json;
        if(url.includes("http")){
           $.ajax({
                type: "GET",
                url: url,
                async: false,
                method: 'GET',
                success: function (response) {
                    json = response;
                   
                },
               error: function (data) {
                   toastr.error('Failed to fetch critical data')
                }
            });
        } else {
            return $.getJSON(url, function (response) {
                return {
                    json:response
                }
           });
        }
        if(!validateJSON(json)){
            json = false;
        }
        return json;
    }
    


    

    function getImages() {
        getJson(jsonURLs.images).then(function (data) {
            vehicleDatabase.images = data;
            mapVehicles();
        });
    }
    getImages();


    function findBestFitMakeImage(manufacture) {
        var highestSimilarity = 0;
        var ProjectedSRC = '/Assets/unknown-make.png';
        var matches = {};
        var ProjectedMake = manufacture;
        $.each(vehicleDatabase.images, function (key, value) {
            var possibleMake = key.toLowerCase();
            var make = manufacture.toLowerCase();
            if (possibleMake[0] === make[0]) {
                var similarity = 0;
                make = make.split("");
                possibleMake = possibleMake.split("");
                $.each(make, function (k, v) {
                    if ($.inArray(v, possibleMake) != -1) {
                        var points = 10 - k >= 0 ? 10 - k : 0;
                        similarity += points;
                    }
                });
                matches[key] = (similarity/(10*make.length)*100).toFixed(2)+'%';
                if (similarity == highestSimilarity) {
                    ProjectedSRC = '/Assets/unknown-make.png';
                }else if (similarity > highestSimilarity) {
                    highestSimilarity = similarity;
                    ProjectedSRC = value;
                    ProjectedMake = key.replace(/_/g, "-");
                }
            }
            
        });
        console.log(manufacture + ' matched best with ' + ProjectedMake + ' by ' + matches[ProjectedMake.replace(/-/g, "_")])
        console.log(matches)
        return {
            src: ProjectedSRC,
            make: ProjectedMake
        };
    }

    function setMakeImage(make,returnMakeImg) {
        var make = make.toUpperCase().replace(/-/g, "_");
        if (!returnMakeImg) {
            $this = $('#' + selectedVehicleId).find('.stored-vehicle-image');
            if (vehicleDatabase.images.hasOwnProperty(make)) {
                $this.attr('src', vehicleDatabase.images[make]);
                $this.removeClass('pending-logo');
            } else {
                var result = findBestFitMakeImage(make);
                $this.attr('src', result.src);
                savedVehicles[selectedVehicleId].make = result.make;
                $this.removeClass('pending-logo');
            }
        } else {
            return vehicleDatabase.images.hasOwnProperty(make) ? vehicleDatabase.images[make] : findBestFitMakeImage(make).src;
        }
        
    }

    function setStates() {
        getJson(jsonURLs.states).then(function (data) {
            vehicleDatabase.state = data;
            setInput('state');
        });
    }

    function setMakes() {
        getJson(jsonURLs.make).then(function (data) {
            var total = 0;
            var makes = data.Results;
            $.each(makes, function (key, value) {
                total += 1;
                if (total <= 100) {
                    vehicleDatabase.make.push(makes[key].Make_Name);
                } else {
                    vehicleDatabase.alt_make.push(makes[key].Make_Name);
                }

            });
            setInput('make');
        });

    }

    setMakes();
    setStates();

    function getNextOptionSet(URL,arrayKey){
        if (URL != false) {
            if (arrayKey !== 'make') {
                vehicleDatabase[arrayKey] = [];
            }
            var options = getJson(URL.replace(/-/g, "_")).Results;
            if (validateJSON(options)) {
                var refreshMakes = false;
                if (options.length == 0) {
                    refreshMakes = true;
                    loadAllMakes();
                    options = getJson(URL).Results;
                }
                $.each(options, function (key, value) {
                    vehicleDatabase[arrayKey].push(options[key][jsonKeys[arrayKey]]);
                });
            }
        }
        setInput(arrayKey);
        if (refreshMakes) {
            setInput('model');
        }
    }

    function modelExist() {
        var result = false
        $.each(vehicleDatabase.model, function (index, value) {
            if (savedVehicles[selectedVehicleId].model.toLowerCase() === value.toLowerCase()) {
                result = true;
                return false;
            }
        });
        return result;
    }


    function editVehicle() {
        getNextOptionSet(jsonURLs.model.replace('_THIS_', savedVehicles[selectedVehicleId].make.toUpperCase()), 'model');
        var vehicleInfo = savedVehicles[selectedVehicleId];
        $.each(vehicleInfo, function (key, value) {
            var $this = $('#' + key);
            if (key === 'make') {
                value = value.replace(/[^a-z0-9\s]/gi, ' ')
            } 
            if (key === 'model') {
                $this.next('small').remove();
                if (!modelExist()) {
                    console.log('The model "' + value + '" is  invaild. Valid models for "' + savedVehicles[selectedVehicleId].make + '" shown below...');
                    console.log(vehicleDatabase.model);
                    $this.val("");
                    $this.css('border', '1px solid red');
                    $this.after('<small class="text-danger" id="invalidModel">The model "' + value + '" is  invaild. Please choose a vaild <span class="text-capitalize">' + savedVehicles[selectedVehicleId].make + '<span> model.</small>');
                } else {
                    $this.removeAttr('style');
                    $this.val(value);
                }
            } else {
                $this.val(value);
            }
        });
        
        
    }




    function addVehicle(){
        $loader.fadeIn();
        $.ajax({
            type: "POST",
            url:'Ajax',
            async: false,
            data: {
                addCar: newVehicle.make,
                Model: newVehicle.model,
                Color: newVehicle.color,
                Plate: newVehicle.plate,
                State: newVehicle.state,
                Year: newVehicle.year
            },
            method: 'POST',
            success: function (data) {
                try {
                    var info = $.parseJSON(data);
                    if (info.result) {
                        toastr.success(info.response);
                        $('#add').text('Just a moment...');
                        setTimeout(function () {
                            window.location.reload();
                        }, 1500);
                    } else {
                        insertingNew = false;
                        toastr.error(info.response);
                    }
                    
                } catch (err) {
                    insertingNew = false;
                    toastr.error('An unknown error occurred');
                    console.log(data);
                }
                
                $loader.fadeOut();
            },error: function (data) {
                $loader.fadeOut();
            }
        });

    }


    function updateVehicle() {
        $loader.fadeIn();
        var $this = $('#' + selectedVehicleId);
        var vehicle = savedVehicles[selectedVehicleId];
        $.each(vehicle, function (key, value) {
            if ($('#' + key).length) {
                var newValue = $('#' + key).val();
                savedVehicles[selectedVehicleId][key] = newValue;
                $this.find('.stored-vehicle-' + key).text(newValue);
                if (key === 'make') {
                    $this.find('.stored-vehicle-make').text(newValue.replace(/-/g, " "));
                    if (newValue.toLowerCase() !== value.toLowerCase()) {
                        setMakeImage(newValue,false);
                    }
                }
            }
        });
        var vehicleMake = vehicle.make.replace(/-/g, "_");

        $.ajax({
            type: "POST",
            url:'Ajax',
            async: false,
            data: {
                updateCar: vehicle.CarID,
                Make: vehicleMake,
                Model: vehicle.model,
                Color: vehicle.color,
                Plate: vehicle.plate,
                State: vehicle.state,
                Year: vehicle.year
            },
            method: 'POST',
            success: function (data) {
                try {
                    var info = $.parseJSON(data);
                    if (info.result) {
                        toastr.success(info.response);
                        
                        hideSideMenu();
                    } else {
                        toastr.error(info.response);
                    }
                } catch (err) {
                    toastr.error('An unkown error occurred');
                }
                $loader.fadeOut();
            }, error: function (data) {
                $loader.fadeOut();
            }
        });
        
    }

    function deleteVehicle() {
        $loader.fadeIn();
        var vehicle = savedVehicles[selectedVehicleId]
        console.log(selectedVehicleId);
        console.log(vehicle);

        $.ajax({
            type: "POST",
            url:'Ajax',
            async: false,
            data: {
                deleteCar: vehicle.CarID,
            },
            method: 'POST',
            success: function (data) {
                try {
                    var info = $.parseJSON(data);
                    if (info.result) {
                        delete savedVehicles[selectedVehicleId];
                        $('#' + selectedVehicleId).remove();
                        mapVehicles();
                        hideDialog(false);
                        toastr.success(info.response);
                    } else {
                        toastr.error(info.response);
                    }
                } catch (err) {
                    toastr.error('An unknow error occurred');
                }
                $loader.fadeOut();
            }, error: function (data) {
                $loader.fadeOut();
            }
        });
    }


    function updateCounts() {
        var searchTerm = $('#vehicle-search').val();
        var searchCatergory = $('#searchCategory').val();
        if (searchTerm.length == 0) {
            $('#searchResultsText').text('Registered vehicles');
            mapVehicles();
            $('#searchHelp').empty();
        } else {
            $('#searchResultsText').text('Results');
            $('#totalVehicles').text(TotalVehicles);
            if (TotalVehicles == 0) {
                var response = searchCatergory === 'all' ? 'No results found.' : 'No results found. Trying changing the search category.';
                $('#searchHelp').html('<div class="alert alert-danger" role="alert">' + response + '</div>')
            } else {
                $('#searchHelp').empty();
            }
        }
    }
    

    $(document).on('input', '.numeric', function () {
        this.value = this.value.replace(/[^0-9\.]/g, '');
    });

    $(document).on('input','.classifier',function(){
        newVehicle[$(this).attr('data-type')] = this.value;
    });

    $(document).on("click", ".dialog-close", function () {
        hideDialog(false);
        if (!quickAction) {
            editVehicle();
            showSideMenu();
        }
        quickAction = false;
    });

    $(document).on("click", "#dialog-confirm", function () {
        deleteVehicle();
    });

    
    $(document).on('click', '.core-action', function (event) {
        event.stopImmediatePropagation();
        if (this.hasAttribute('data-RID')) {
            selectedVehicleId = $(this).attr('data-RID');
            quickAction = true;
        }
        switch ($(this).attr('data-action')) {
            case 'add':
                if (!highlightMissingFields({
                    parent: 'newVehicleForm',
                    targetClass: 'form-control'
                }) && NewVehicleIsFilled()) {
                    if (!insertingNew) {
                        $loader.fadeIn("slow", function () {
                            insertingNew = true;
                            addVehicle();
                        });
                    }
                }
                break;
            case 'edit':
                if (!highlightMissingFields({
                    parent: 'editVehicleForm',
                    targetClass: 'form-control'
                })) {
                    updateVehicle();
                }
                break;
            case 'delete':
                hideSideMenu();
                showDialog(quickAction);
                break;
        }

    });

    $(document).on('click', '.stored-vehicle', function () {
        if (!AdminMode) {
            quickAction = false;
            selectedVehicleId = this.id;
            $loader.fadeIn("slow", function () {
                editVehicle();
                showSideMenu();
                checkInputs();
            });
        }
    });

    $(document).on('click', '.close-side-menu,.overlay', function () {
        checkInputs();
        hideSideMenu();

    });

    

    $(document).on('click', '.loadAllMakes', function () {
        var $this = $(this);
        loadAllMakes()
        $this.text('Try searching now');
        toastr.info('Manufacturer list updated')
        setTimeout(function (){
            $('.loadAllMakes').remove();
        }, 2000);
    });




    $(document).on('change', '#searchCategory', function () {
        $('#vehicle-search').trigger('input');
    });

    $(document).on('input', '#vehicle-search', function () {
        TotalVehicles = 0;
        var filter = this.value;
        var searchCategory = $('#searchCategory').val();
        if (timeout) { clearTimeout(timeout); }
            timeout = setTimeout(function () {
            $('.tile').each(function () {
                var result = searchCategory === 'all' ? $(this).find('span').text().search(new RegExp(filter, "i")) < 0 : $(this).find('.' + searchCategory).text().search(new RegExp(filter, "i")) < 0;
                if (result) {
                    $(this).hide();
                } else {
                    $(this).show();
                    TotalVehicles += 1;
                }

            });
            updateCounts();
        }, 500);

    });

});