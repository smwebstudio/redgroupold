@extends('layouts.app')

@push('body-class', 'project-archive')
@push('header-class', 'header-trans')

@section('content')
    <div class="page-top" style="position: relative;">
        @include('includes.title_block')
        @include('includes.filterproj')
    </div>
    <div class="container">
        <div id="app" class="row project--archive">
            <div class="col-md-4 d-none d-lg-block site-sidebar">
                @include('includes.sidebar')
            </div>

            <div class="col-md-8 site-content">
                <div id="projects" class="row">
                    @php
                        $coords = [];
                        $currency_rate = 'amd';
                        if(isset($_GET['currency_rate'])){
                            $currency_rate = $_GET['currency_rate'];
                        }
                    @endphp
                    @foreach($projects as $proj)

                        <div class="project col-sm-6 pb-3 px-2" id="{{$proj->id}}">
                            <a href="{{ route('projects.single', ['slug' => $proj->slug ]) }}?currency_rate={{$currency_rate}}#{{$proj->id}}">
                                @php
                                        $data = isset($proj->main_project)  ? $proj->main_project->created_at : $proj->created_at;
                                        $projectDate = new DateTime($data);
                                        $nowDate = new DateTime();
                                        $interval = $projectDate->diff( $nowDate);
                                        $days = $interval->format('%a');

                                @endphp
                                @if(!$proj->sold)
                                @if(((int)$days < (int)$project_new_days) && $proj->top)

                                        <div class="projects-new-and-top"><img class="project-top-img" src="{{ asset("/storage/projects/topstar.png")}}"><p>{{__('projects.new')}}</p></div>
                                @elseif((int)$days < (int)$project_new_days)
                                    <div class="projects-new">{{__('projects.new')}}</div>
                                @elseif($proj->top)
                                    <div class="projects-top"><img class="project-top-img" src="{{ asset("/storage/projects/topstar.png")}}"></div>
                                @endif
                                @endif
                                @if($proj->viewed)
                                    <div class="projects-viewed"><i class="fa-eye far"></i>{{$proj->viewed}}</div>
                                @endif
                                @if (isset($proj->featured_image))
                                    <img class="lazy-image" data-src="{{  $proj->featured_image }}"
                                         alt="{{ $proj->file()->first() ? $proj->file()->first()['alt'] : '' }}">
                                @elseif ($proj->file()->first() != null)
                                    <img class="lazy-image" data-src="{{ $proj->file()->first()['url'] }}"
                                         alt="{{ $proj->file->first()['alt'] }}">
                                @endif
                                @if($proj->status == 'sold')
                                    <img src="{{ asset('storage/sold.png') }}" class="archive_img"
                                         alt="{{ $proj->file()->first() ? $proj->file()->first()['alt'] : '' }}">
                                @endif
                                <div class="project-title">
                                    {{ $proj->title }}
                                </div>
                            </a>
                        </div>
                        @php
                            if (isset($proj->coordinates[0])) {
                                $coordinates = explode(',', $proj->coordinates);
                                if (isset($coordinates[1])) {
                                    array_push($coords, [(float)$coordinates[0],(float)$coordinates[1]]);
                                }
                            }
                        @endphp
                    @endforeach
                    @php
                        $coords = json_encode($coords);
                    @endphp
                </div>
                @if (count($projects) < $projects->total())
                    <div class="load-more-btn text-center">
                        <button id="load_more_button" data-url="{{ $projects->nextPageUrl() }}">{{ __('projects.load_more') }}</button>
                    </div>
                @endif
            </div>
        </div>

    </div>


    <div id="map" style="width: 100%; height: 20vw;" class="map mt-3 col-12"></div>
@endsection

@php
    $locale = app()->getLocale();
    switch ($locale) {
        case 'hy':
            $map_lang = 'hy_AM';
            break;
        case 'ru':
            $map_lang = 'ru_RU';
            break;
        case 'en':
            $map_lang = 'en_GB';
            break;
        default:
            $map_lang = 'en_US';
    }

@endphp
@section('script')
    <script src="https://api-maps.yandex.ru/2.1/?apikey=<your API key>&lang={{ $map_lang }}" type="text/javascript">
    </script>
        <script defer src="{{ asset('js/bootstrap-multiselect.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#projec_names').multiselect({
                buttonWidth: '100%',
                includeSelectAllOption: true,
                nonSelectedText: '{{__("projects.select_project")}}',
                selectAllText: '{{__("projects.select_all")}}',
                allSelectedText: '{{__("projects.select_all")}}',
                nSelectedText: '{{__("projects.selected")}}'
            }).attr('hidden', false);
            $('#project_rooms').multiselect({
                buttonWidth: '100%',
                includeSelectAllOption: true,
                nonSelectedText: '{{__("projects.select_rooms")}}',
                selectAllText: '{{__("projects.select_all")}}',
                allSelectedText: '{{__("projects.select_all")}}',
                nSelectedText: '{{__("projects.selected")}}'
            }).attr('hidden', false);
        });

        $(document).ready(function () {
            let images = $('.lazy-image');
            images.each(function (index, el) {
                $(el).attr('src', $(this).data('src'));
            });
        });
    </script>
    <script defer type="text/javascript">
    
    $(document).on('click', '#load_more_button', function () {
        var url = $(this).data('url');
        var now = new Date();
        console.log(now.getTime());
        $.ajax({
            url: url,
            method: 'GET',
            data: {
                mode: 'load_more'
            },
            success: function (data) {
                let response = data.projects
                let project_new_days = data.project_new_days

                $('#load_more_button').data('url', response.next_page_url);
                let res = response.data;
                let projects = '';
                let days = {{ isset($days) ? $days : 90 }};
                for (var i = 0; i < res.length; i++) {
                    let date = res[i].main_project ? res[i].main_project.created_at : res[i].created_at
                    let crate_date = new Date(date);
                    let days = parseInt((now.getTime()-crate_date.getTime())/(24*3600*1000));
                    let projects_new_and_top = "";
                    let projects_viewed = "";
                    if (res[i].status != 'sold') {
                        if (days < parseInt(project_new_days) && res[i].top) {
                            projects_new_and_top = '<div class="projects-new-and-top"><img class="project-top-img" src="{{ asset("/storage/projects/topstar.png")}}"><p>{{__('projects.new')}}</p></div>'
                        } else if (days < parseInt(project_new_days)) {
                            projects_new_and_top = '<div class="projects-new">{{__('projects.new')}}</div>'
                        } else if (res[i].top) {
                            projects_new_and_top = '<div class="projects-top"><img class="project-top-img" src="{{ asset("/storage/projects/topstar.png")}}"></div>'
                        }
                    }

                    if(res[i].viewed){
                        projects_viewed = '<div class="projects-viewed"><i class="fa-eye far"></i>'+res[i].viewed+'</div>'
                    }

                    projects += '<div id="' + res[i].id + '" class="project col-sm-6 pb-3 px-2" id="' + res[i].id + '">' +
                        '<a href="' + window.location.origin + '/projects/' + res[i].slug + '">' +
                        projects_new_and_top  +
                        projects_viewed  +
                        '<img class="lazy-image" src="' + res[i].featured_image + '" alt="">' +
                        (res[i].status == 'sold' ? '<img src="{{ asset('storage/sold.png') }}" class="archive_img" alt="">' : '') +
                        '<div class="project-title">' + res[i].title + '</div>' +
                        '</a>' +
                        '</div>';
                }
                // console.log($('.project').length + '-' + res.length);
                $('#projects').append(projects);
                if ($('.project').length >= response.total) {
                    $('#load_more_button').remove();
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    });

        function init() {
            var myMap = new ymaps.Map("map", {
                center: [40.180521, 44.522904],
                zoom: 10
            });
            @if (isset($projectsAllCoordinates))
                @foreach ($projectsAllCoordinates as $project)
                @if($project->coordinates)
                myMap.geoObjects.add(new ymaps.Placemark([{{ $project->coordinates }}], {
                    balloonContent: ['<a class="item-in-baloon" href="{{ route('projects.single', ['slug' => $project->slug ]) }}" target="_blank"><img src="{{ $project->featured_image }}" alt="{{ $project->title ?? '' }}"><p class="baloon-item-address">{{ $project->address ?? '' }}</p></a>']
                }, {
                    preset: 'islands#icon',
                    iconColor: '#EA3C36'
                }));
                @endif
                @endforeach
            @else
                @foreach ($projects as $project)
                @if($project->coordinates)
                    myMap.geoObjects.add(new ymaps.Placemark([{{ $project->coordinates }}], {
                        balloonContent: ['<a class="item-in-baloon" href="{{ route('projects.single', ['slug' => $project->slug ]) }}" target="_blank"><img src="{{ $project->featured_image }}" alt="{{ $project->title ?? '' }}"><p class="baloon-item-address">{{ $project->address ?? '' }}</p></a>']
                    }, {
                        preset: 'islands#icon',
                        iconColor: '#EA3C36'
                    }));
                @endif
                @endforeach
            @endif
        }

        ymaps.ready(init);


{{--        @if(((int)$days < (int)$project_new_days) && $proj->top)--}}

        {{--<div class="projects-new-and-top"><img class="project-top-img" src="{{ asset("/storage/projects/Top_star.svg")}}"><p>{{__('projects.New')}}</p></div>--}}
        {{--@elseif((int)$days < (int)$project_new_days)--}}
        {{--<div class="projects-new">{{__('projects.New')}}</div>--}}
        {{--@elseif($proj->top)--}}
        {{--<div class="projects-top"><img class="project-top-img" src="{{ asset("/storage/projects/Top_star.svg")}}"></div>--}}
        {{--@endif--}}
        {{--@if($proj->viewed)--}}
        {{--<div class="projects-viewed"><i class="fa-eye far"></i>{{$proj->viewed}}</div>--}}
        {{--@endif--}}
        // $('#save_req').on('click', function(event) {
        // 	event.preventDefault();
        // 	let formData = $('#req_form').serialize();
        // 	$.ajax({
        // 		url: '{{route("request.save")}}',
        // 		method: 'POST',
        // 		data: {
        // 			_token: $('meta[name="csrf-token"]').attr('content'),
        // 			form_data: formData
        // 		},
        // 		success: function(res) {
        // 			if (res.errors) {
        // 				for (let i in res.errors) {
        // 					console.log('.' + i);
        // 					$('.' + i).text(res.errors[i]);
        // 					$('.' + i).show();
        // 				}
        // 			} else {
        // 				$('#reqModal').modal('hide');
        // 			}
        // 		},
        // 		error: function(error) {
        // 			console.log(error);
        // 		}
        // 	})
        // });

        // $('#req_form input, #req_form select').on('input', function(event) {
        // 	$(this).siblings('.invalid-feedback').hide();
        // });

    </script>
@endsection
