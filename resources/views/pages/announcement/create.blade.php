@extends('layouts.app')
{{-- @include('includes/header') --}}
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->messages() as $key => $error)
                    @if(is_array($error))
                        @foreach ($error as $r)
                
                            <li>{{ $r }} </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
    <div class="announcement create container">
        <h1>{{ __('new_app.title') }}</h1>
       <form class="new-app-form" action="{{ url('/').'/'.app()->getLocale().'/announcement/store_client_side' }}"
       {{-- <form class="new-app-form" action="{{ env('ANNOUNCEMENT_STORE_API').app()->getLocale().'/announcement/store_client_side' }}"--}}
        {{-- <form class="new-app-form" action="{{ route('announcement.store') }}" --}}
              method="post" enctype="multipart/form-data">
            @method('POST')
            @csrf
            <label>{{ __('common.personal_data') }}</label>
            <div class="personal-data form-row">
                <div class="col-md-4">
                    <label for="name" class="required">{{ __('common.name') }} {{ __('common.last_name') }}</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="{{ __('common.name') }}" value="{{ old('name') }}">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="tel" class="required">{{ __('common.phone_number') }}</label>
                    <input type="tel" id="tel" name="tel" class="form-control @error('tel') is-invalid @enderror"
                           placeholder="+374" value="{{ old('tel') }}">
                    @error('tel')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="email" class="required opt villa ">{{ __('common.mail') }}</label>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="{{ __('common.mail') }}" value="{{ old('email') }}">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div><!-- .personal-data-->
            
            
            <label>{{ __('common.property_description') }}</label>
            <div class="property-description form-row">
                <div class="col-md-6">
                    <label for="property_type" class="required">{{ __('common.property_type') }}</label>
                    <select id="property_type" name="property_type_id"
                            class="form-control @error('property_type_id') is-invalid @enderror">
                        @foreach ($property_types as $property_type)
                            <option value="{{ $property_type->id }}"
                                    @if(old('property_type_id') == $property_type->id) selected @endif>
                                {{ $property_type->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="announcement_type_id" class="required">{{ __('common.contract_type') }}</label>
                    <select id="announcement_type_id" name="announcement_type_id"
                            class="form-control @error('announcement_type_id') is-invalid @enderror">
                        @foreach ($announcement_types as $announcement_type)
                            <option value="{{ $announcement_type->id }}"
                                    @if(old('announcement_type_id') == $announcement_type->id) selected @endif>
                                {{ $announcement_type->title }}</option>
                        @endforeach
                    </select>
                    @error('announcement_type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div><!-- .property-description -->
            <hr>
            <label>{{ __('common.address') }}</label>
            <div class="address-block form-row">
                @foreach ($place_types as $i => $place_type)
                    
                    <div class="col-md-4">
                        <label for="{{ $place_type->title }}"
                               class="required">{{ $place_type->title }}</label>
                        <select id="child_{{ $i }}" name="{{ $place_type->title }}" data-child="child_{{ $i+1 }}"
                                class="form-control @error($place_type->title) is-invalid @enderror">
                            <option disabled selected hidden>{{ __('common.choose') }}</option>
                            @foreach ($place_type->places as $place)
                                <option
                                    value="{{ $place->id }}" data-parent_id="{{ $place->parent_id }}"  @if(old($place_type->title) == $place->id) selected @endif>
                                    {{ $place->name }}
                                </option>
                            @endforeach
                        </select>
                        @error($place_type->title)
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                
                @endforeach
                <div class="col-md-4 choose-position">
                    <label for="place_id" class="required">{{ __('common.street') }}</label>
                    <!-- <input type="text" id="street" name="street"
                           class="form-control @error('street') is-invalid @enderror" value="{{ old('street') }}"> -->
                        <select class="form-control" id="child_2" name="place_id" data-child="place_id">
                            <option value="">{{ __('common.choose') }}</option>
                        </select>
                    @error('street')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="building" class="required">{{ __('common.building') }}</label>
                    <input type="text" id="building" name="building"
                           class="form-control @error('building') is-invalid @enderror" value="{{ old('building') }}">
                    @error('building')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3">
                    <label for="apartment" class="required">{{ __('common.apartment') }}</label>
                    <input type="text" id="apartment" name="apartment"
                           class="form-control @error('apartment') is-invalid @enderror" value="{{ old('apartment') }}">
                    @error('apartment')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 opt apt">
                    <label for="floor" class="opt apt">{{ __('common.floor') }}</label>
                    <input type="number" id="floor" name="floor" step="1"
                           class="form-control opt apt @error('floor') is-invalid @enderror" value="{{ old('floor') }}">
                    @error('floor')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-3 opt apt">
                    <label for="building_floor" class="opt apt">{{ __('common.building_floor_count') }}</label>
                    <input type="number" id="building_floor" name="building_floor" step="1"
                           class="form-control opt apt @error('building_floor') is-invalid @enderror"
                           value="{{ old('building_floor') }}">
                    @error('building_floor')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 opt apt">
                    <label for="ceiling_height">{{ __('common.ceiling_height') }}</label>
                    <select id="ceiling_height" name="ceiling_height_id"
                            class="form-control @error('ceiling_height_id') is-invalid @enderror">
                        <option disabled selected hidden>{{ __('common.choose') }}</option>
                        @foreach ($ceiling_heights as $ceiling_height)
                            <option
                                value="{{ $ceiling_height->id }}"
                                @if(old('ceiling_height_id') == $ceiling_height->id) selected @endif>
                                {{ $ceiling_height->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('ceiling_height_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="rooms_number">{{ __('common.rooms') }}</label>
                    <input type="number" id="rooms_number" name="rooms" min="0" step="1"
                           class="form-control @error('rooms') is-invalid @enderror"
                           value="{{ old('rooms', 0) }}">
                    @error('rooms')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="area" class="opt apt villa land comm">{{ __('common.area') }}</label>
                    <input type="number" id="area" name="area"
                           class="form-control opt apt villa land comm @error('area') is-invalid @enderror"
                           placeholder="{{ __('common.area_point') }}" value="{{ old('area') }}">
                    @error('area')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <label for="land_area" class="opt villa" style="display: none;">{{ __('common.land_area') }}</label>
                    <input type="number" id="land_area" style="display: none;" disabled name="land_area"
                           class="form-control opt villa @error('land_area') is-invalid @enderror"
                           placeholder="{{ __('common.area_point') }}" value="{{ old('land_area') }}">
                    @error('land_area')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label for="price">{{ __('common.price') }}</label>
                    <input type="number" id="price" name="price" min="0" step="1" value="{{ old('price', 0) }}"
                           class="form-control @error('price') is-invalid @enderror">
                    @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <select class="form-control @error('price_currency_id') is-invalid @enderror" id="price_currency"
                            name="price_currency_id">
                        @foreach ($currencies as $currency)
                            <option
                                value="{{ $currency->id }}"
                                @if(old('price_currency_id') == $currency->id)  selected @endif>
                                {{ $currency->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('price_currency_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4">
                    <label for="service_fee">{{ __('common.service_fee') }}</label>
                    <input type="number" id="service_fee" name="service_fee"
                           min="0" step="1" value="{{ old('service_fee', 0) }}"
                           class="form-control @error('service_fee') is-invalid @enderror">
                    @error('service_fee')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <select class="form-control @error('service_fee_currency_id') is-invalid @enderror"
                            id="service_fee_currency"
                            name="service_fee_currency_id">
                        @foreach ($currencies as $currency)
                            <option
                                value="{{ $currency->id }}"
                                @if(old('service_fee_currency_id') == $currency->id) selected @endif>
                                {{ $currency->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_fee_currency_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                
                </div>
            </div><!-- .address-block -->
            
            <div class="images-list form-row">
                <div class="images">
                    <div class="img-item no-image">
                        <img src="{{ url('storage/no_image.jpg') }}" alt="No image">
                    </div>
                    <div class="button-block">
                        <button class="button upload-images-btn"
                                id="upload_images">{{ __('common.upload_images') }}</button>
                    </div>
                </div>
                <input type="file" id="images" name="image[]" multiple hidden>
                @error('images[]')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            
            </div><!-- .images-list -->
            
            <hr>
            
            <label>{{ __('common.announcement') }}</label>
            <div class="apartment">
                @foreach ($property_types as $property_type)
                    
                    <div class="property-item-opt" style="display:none;">
                        <input type="text" class="property-type-id" value="{{ $property_type->id }}" hidden disabled>
                        <div class="form-row">
                            @foreach ($announcement_option_types as $ann_opt_type)
                                @if(in_array($ann_opt_type->id, unserialize($property_type->allowed_fields)))
                                    <div class="col-md-3 col-6">
                                        <label
                                            for="ann_opt_type_{{ $ann_opt_type->id }}">{{ $ann_opt_type->title }}</label>
                                        <select name="option_type[]"
                                                id="ann_opt_type_{{ $ann_opt_type->id }}"
                                                class="form-control @error('option_type[]') is-invalid @enderror">
                                            <option disabled selected hidden>{{ __('common.choose') }}</option>
                                            @foreach ($ann_opt_type->options as $ann_opt)
                                                <option
                                                    value="{{ $ann_opt->id }}"
                                                    @if(is_array(old('option_type')) && in_array($ann_opt->id, old('option_type'))) selected
                                                    @endif>
                                                    {{ $ann_opt->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('option_type[]')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                
                @endforeach
            </div><!-- .apartment -->
            <hr>
            <label>{{ __('common.additional') }}</label>
            <div class="additional-options form-row">
                @foreach ($additional as $adds)
                    @foreach ($adds->options as $add_opt)
                        <div class="col-md-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input @error('option_type[]') is-invalid @enderror"
                                       name="option_type[]"
                                       id="add_opt_{{ $add_opt->id }}"
                                       @if(is_array(old('option_type')) && in_array($add_opt->id, old('option_type'))) checked
                                       @endif
                                       value="{{ $add_opt->id }}">
                                <label class="custom-control-label" for="add_opt_{{ $add_opt->id }}">{{ $add_opt->title }}</label>
                            </div>
                            @error('option_type[]')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endforeach
                @endforeach
            
            </div><!-- .additional-options -->
            
            <label>{{ __('common.map') }}</label>
            <div class="map form-row">
                <div id="map" style="width: 100%; height: 400px"></div>
                <input type="text" name="coords" hidden class="@error('coords') is-invalid @enderror"
                       value="{{ old('coords') }}">
                @error('coords')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="submit-block input-group d-flex justify-content-end">
                <input type="reset" class="button reset-btn" value="{{ __('common.reset') }}">
                <input type="submit" class="button submit-btn" value="{{ __('common.add') }}">
            </div>
        </form><!-- form -->
    </div><!-- .new-app .container -->
@endsection
@section('script')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=<your API key>&lang=en_US" type="text/javascript"></script>
    <script>

        // Open file reader
        $('#upload_images').on('click', function (e) {
            e.preventDefault();
            $('#images').click();
        });

        // Add images to images block
        $('#images').change(function () {
            readURL(this);
        });

        //Remove images from images block
        $('.images').on('click', '.remove-image', function () {
            $(this).closest('div.img-item').remove();
            let imgCount = parseInt($('.img-item').length);
            if (imgCount < 2) {
                $('.img-item.no-image').css('display', 'block');
            }
        });

        // Property type Select change event
        $('.apartment .property-item-opt:first-child').css('display', 'block');

        let select_prop_type_id = $('select[name="property_type_id"]');

        disableOptionTypes(select_prop_type_id.val(), $('input.property-type-id'));

        select_prop_type_id.on('change', function () {
            let id = $(this).val();
            let prop_block_id = $('input.property-type-id');

            disableOptionTypes(id, prop_block_id);

            $('.opt').css('display', 'none').attr('disabled', 'disabled');
            if (id === '1') {
                $('.opt.apt').css('display', 'block').removeAttr('disabled');
                $('label[for="building"]').text('{{ __("common.building") }}');
                $('label[for="apartment"]').text('{{ __("common.apartment") }}');
                $('label[for="building_floor_count"]').text('{{ __("common.building_floor_count") }}');
            } else if (id === '2') {
                $('.opt.villa').css('display', 'block').removeAttr('disabled');
                $('label[for="building"]').text('{{ __("common.building") }}');
                $('label[for="apartment"]').text('{{ __("common.certificate") }}');
            } else if (id === '3') {
                $('.opt.comm').css('display', 'block').removeAttr('disabled');
                $('label[for="building"]').text('{{ __("common.building") }}');
                $('label[for="apartment"]').text('{{ __("common.certificate") }}');
                $('label[for="rooms_number"]').text('{{ __("common.halls") }}');
            } else if (id === '4') {
                $('.opt.land').css('display', 'block').removeAttr('disabled');
                $('label[for="building"]').text('{{ __("common.land") }}');
                $('label[for="apartment"]').text('{{ __("common.certificate") }}');
            }
        });

        // Disables Select option types for non selected property types
        function disableOptionTypes(prop_type_id, prop_block_id) {
            prop_block_id.parent('.property-item-opt').css('display', 'none').find('select').attr('disabled', 'disabled');
            prop_block_id.each(function (key, value) {
                if (value.value === prop_type_id) {
                    $(value).parent('.property-item-opt').css('display', 'block').find('select').removeAttr('disabled');
                }
            });
        }

        // Show images in browser after upload
        function readURL(input) {
            let buttonBlock = $('div.button-block');
            if (input.files) {
                for (let i = input.files.length - 1; i >= 0; i--) {
                    if (input.files[i]) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            buttonBlock.before(
                                '<div class="img-item">' +
                                '<img src="' + e.target.result + '">' +
                                '<i class="far fa-window-close remove-image"></i>' +
                                '</div>'
                            );
                            let imgCount = parseInt($('.img-item').length);
                            if (imgCount > 1) {
                                $('.img-item.no-image').css('display', 'none');
                            }
                        };

                        reader.readAsDataURL(input.files[i]);
                    }
                }
            }
        }

        // The ymaps.ready() function will be called when
        // all the API components are loaded and the DOM tree is generated.
        ymaps.ready(init);

        function init() {
            var myPlacemark,
                myMap = new ymaps.Map('map', {
                    center: [40.174197, 44.447724],
                    zoom: 9
                }, {
                    searchControlProvider: 'yandex#search'
                });

            // Listening for a click on the map
            myMap.events.add('click', function (e) {
                var coords = e.get('coords');
                $('input[name="coords"]').val(coords.toString());
                // Moving the placemark if it was already created
                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                }
                // Otherwise, creating it.
                else {
                    myPlacemark = createPlacemark(coords);
                    myMap.geoObjects.add(myPlacemark);
                    // Listening for the dragging end event on the placemark.
                    myPlacemark.events.add('dragend', function () {
                        getAddress(myPlacemark.geometry.getCoordinates());
                    });
                }
                getAddress(coords);
            });

            // Creating a placemark
            function createPlacemark(coords) {
                return new ymaps.Placemark(coords, {
                    iconCaption: 'searching...'
                }, {
                    preset: 'islands#violetDotIconWithCaption',
                    draggable: true
                });
            }

            // Determining the address by coordinates (reverse geocoding).
            function getAddress(coords) {
                myPlacemark.properties.set('iconCaption', 'searching...');
                ymaps.geocode(coords).then(function (res) {
                    var firstGeoObject = res.geoObjects.get(0);

                    myPlacemark.properties
                        .set({
                            // Forming a string with the object's data.
                            iconCaption: [
                                // The name of the municipality or the higher territorial-administrative formation.
                                firstGeoObject.getLocalities().length ? firstGeoObject.getLocalities() : firstGeoObject.getAdministrativeAreas(),
                                // Getting the path to the toponym; if the method returns null, then requesting the name of the building.
                                firstGeoObject.getThoroughfare() || firstGeoObject.getPremise()
                            ].filter(Boolean).join(', '),
                            // Specifying a string with the address of the object as the balloon content.
                            balloonContent: firstGeoObject.getAddressLine()
                        });
                });
            }
        }

        $(document).ready(function() {

            $('#child_2').select2({
              dropdownParent: $(".choose-position")
            });
     
            // State change
            $('#child_0').on('change', function () {
                ajaxCall($(this));
            });

            // Community change
            $('#child_1').on('change', function () {
                ajaxCall($(this));
            });


            function ajaxCall(element) {
                $.ajax({
                    method: "POST",
                    url: '{{ route("announcement.get_place_children", ["locale" => app()->getLocale()]) }}',
                    dataType: "JSON",
                    data: {
                        '_token': $('input[name="_token"]').val(),
                        'parent_id': element.val()
                    },
                    success: function (response) {
                        successCallback(response, element);
                    }
                });
            }

            function successCallback(response, element) {
                addOptions(response, element);
            }

            function addOptions(response, element) {

                if (response !== undefined) {

                    let elementOptions = '<option value="">{{ __("common.choose") }}</option>';
                    let child_name = element.data('child');
                    let child = $('#' + child_name);
                    let condition = true;

                    $.each(response, function (key, value) {

                        let selected = '';
                        if (value['children']) {

                            if (value['children'].length > 0 && condition) {
                                let _child = $('#' + child_name);
                                addOptions(value.children, _child);
                                condition = false;
                                selected = 'selected';
                            }

                        }
                        elementOptions += '<option value="' + value['id'] +
                            '" data-parent_id="' + value['parent_id'] + '">' + value['name'] + '</option>';
                    });
                    child.html(elementOptions);
                }
            }
        });

    </script>
@endsection
