<?php
namespace Modules\Template\Blocks;

use Modules\Location\Models\Location;
use Modules\Media\Helpers\FileHelper;
use Modules\Tour\Models\TourCategory;

class FormSearchAllService extends BaseBlock
{
    public function getName()
    {
        return __('Form Search All Service');
    }

    public function getOptions()
    {
        $list_service = [];
        foreach (get_bookable_services() as $key => $service) {
            $list_service[] = ['value'   => $key,
                'name' => ucwords($key)
            ];
            $arg[] = [
                'id'        => 'title_for_'.$key,
                'type'      => 'input',
                'inputType' => 'text',
                'label'     => __('Title for :service',['service'=>ucwords($key)])
            ];
        }
        $arg[] = [
            'id'            => 'service_types',
            'type'          => 'checklist',
            'listBox'          => 'true',
            'label'         => "<strong>".__('Service Type')."</strong>",
            'values'        => $list_service,
        ];

        $arg[] = [
            'id'        => 'title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Title')
        ];
        $arg[] = [
            'id'        => 'sub_title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Sub Title')
        ];

        $arg[] =  [
            'id'            => 'style',
            'type'          => 'radios',
            'label'         => __('Style Background'),
            'values'        => [
                [
                    'value'   => '',
                    'name' => __("Normal")
                ],
                [
                    'value'   => 'carousel',
                    'name' => __("Slider Carousel")
                ],
                [
                    'value'   => 'carousel_v2',
                    'name' => __("Slider Carousel Ver 2")
                ],
                [
                    'value'   => 'bg_video',
                    'name' => __("Background Video")
                ],
            ]
        ];

        $arg[] = [
            'id'    => 'bg_image',
            'type'  => 'uploader',
            'label' => __('- Layout Normal: Background Image Uploader')
        ];

        $arg[] = [
            'id'        => 'video_url',
            'type'      => 'input',
            'inputType' => 'text',
            'label' => __('- Layout Video: Youtube Url')
        ];

        $arg[] = [
            'id'          => 'list_slider',
            'type'        => 'listItem',
            'label'       => __('- Layout Slider: List Item(s)'),
            'title_field' => 'title',
            'settings'    => [
                [
                    'id'        => 'title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title (using for slider ver 2)')
                ],
                [
                    'id'        => 'desc',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Desc (using for slider ver 2)')
                ],
                [
                    'id'    => 'bg_image',
                    'type'  => 'uploader',
                    'label' => __('Background Image Uploader')
                ]
            ]
        ];

        $arg[] = [
            'type'=> "checkbox",
            'label'=>__("Hide form search service?"),
            'id'=> "hide_form_search",
            'default'=>false
        ];

        return [
            'settings' => $arg,
            'category'=>__("Other Block")
        ];
    }

    public function hotelLocations()
    {
        $locationsWithHotles = Location::with(['children' => function ($query) {
            $query->where('status', 'publish')
                  ->whereHas('hotels', function ($subQuery) {
                      $subQuery->where('status', 'publish');
                  });
        }, 'children.hotels' => function ($query) {
            $query->where('status', 'publish');
        }])
        ->where('status', 'publish')
        ->whereNull('parent_id')
        ->get();

        $listLocationWithHotels = [];
        foreach ($locationsWithHotles as $location) {
            $country = clone $location;

            $citiesWithHotels = [];
            foreach ($location->children as $city) {
                if ($city->hotels->isNotEmpty()) {
                    $citiesWithHotels[] = $city;
                }
            }

            if (!empty($citiesWithHotels)) {
                $country->cities = $citiesWithHotels;
                $listLocationWithHotels[] = $country;
            }
        }
        return $listLocationWithHotels;
    }

    public function tourLocations()
    {
        $locationsWithTours = Location::with(['children' => function ($query) {
            $query->where('status', 'publish')
                  ->whereHas('tours', function ($subQuery) {
                      $subQuery->where('status', 'publish')
                        ->where(function ($query) {
                            $query->where('default_state', 1)
                                ->orWhere(function ($subQuery) {
                                    $subQuery->where('default_state', 0)
                                        ->whereHas('tour_dates', function($tourDatesQuery) {
                                            $tourDatesQuery->where('active', 1)
                                                ->where('start_date', '>=', now());
                                        });
                                });
                        });
                  });
        }, 'children.tours' => function ($query) {
            $query->where('status', 'publish');
        }])
        ->where('status', 'publish')
        ->whereNull('parent_id')
        ->get();

        $listLocationWithTours = [];
        foreach ($locationsWithTours as $location) {
            $country = clone $location;

            $citiesWithTours = [];
            foreach ($location->children as $city) {
                if ($city->tours->isNotEmpty()) {
                    $citiesWithTours[] = $city;
                }
            }

            if (!empty($citiesWithTours)) {
                $country->cities = $citiesWithTours;
                $listLocationWithTours[] = $country;
            }
        }
        return $listLocationWithTours;
    }

    public function carLocations()
    {
        $locationsWithCars = Location::with(['children' => function ($query) {
            $query->where('status', 'publish')
                  ->whereHas('cars', function ($subQuery) {
                      $subQuery->where('status', 'publish')
                        ->where(function ($q) {
                            $q->where('default_state', 1)
                                ->orWhere(function ($subQuery) {
                                    $subQuery->where('default_state', 0)
                                        ->whereHas('car_dates', function($carDatesQuery) {
                                            $carDatesQuery->where('active', 1)
                                                ->where('start_date', '>=', now());
                                        });
                                });
                        });
                  });
        }, 'children.cars' => function ($query) {
            $query->where('status', 'publish');
        }])
        ->where('status', 'publish')
        ->whereNull('parent_id')
        ->get();

        $listLocationWithCars = [];
        foreach ($locationsWithCars as $location) {
            $country = clone $location;

            $citiesWithHotels = [];
            foreach ($location->children as $city) {
                if ($city->hotels->isNotEmpty()) {
                    $citiesWithHotels[] = $city;
                }
            }

            if (!empty($citiesWithHotels)) {
                $country->cities = $citiesWithHotels;
                $listLocationWithCars[] = $country;
            }
        }
        return $listLocationWithCars;
    }

    public function content($model = [])
    {
        $model['bg_image_url'] = FileHelper::url($model['bg_image'] ?? "", 'full') ?? "";
        $model['list_location'] = $model['tour_location'] = Location::where("status", "publish")->limit(1000)->with(['translation'])->get()->toTree();
        $model['list_location_hotel'] = $model['hotel_location'] = $this->hotelLocations();
        $model['list_location_tour'] = $model['tour_location'] = $this->tourLocations();
        $model['list_location_car'] = $model['car_location'] = $this->carLocations();
        $model['tour_category'] = TourCategory::where('status', 'publish')->with(['translation'])->get()->toTree();
        $model['style'] = $model['style'] ?? "";
        $model['list_slider'] = $model['list_slider'] ?? "";
        $model['modelBlock'] = $model;
        return $this->view('Template::frontend.blocks.form-search-all-service.index', $model);
    }

    public function contentAPI($model = []){
        if (!empty($model['bg_image'])) {
            $model['bg_image_url'] = FileHelper::url($model['bg_image'], 'full');
        }
        return $model;
    }
}
