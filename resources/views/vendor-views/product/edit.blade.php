@extends('layouts.vendor.app')

@section('title', request()->product_gellary == 1 ? translate('Add item') : translate('Update_item'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
@php($module_type = \App\CentralLogics\Helpers::get_store_data()->module->module_type)
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{ request()->product_gellary == 1 ? translate('Add_item') : translate('item_update') }}
                </span>
            </h1>
        </div>

        @if (isset($temp_product) && $temp_product == 1 && $product->note)
            <div class="card-header border-0 align-items-start flex-wrap">
                <div class="order-invoice-left d-flex d-sm-block justify-content-between">
                    <div class="d-flex align-items-center __gap-5px">
                        <h1 class="page-header-title text-danger ">
                        {{ translate('messages.Rejection_Note') }} :
                    </h1>
                        <h3 class="">
                            {{ $product->note }}
                        </h3>
                    </div>
                </div>
            </div>
        @endif
        <!-- End Page Header -->
        <form action="javascript:" method="post" id="product_form"
                enctype="multipart/form-data">
                @csrf


            @if (request()->product_gellary  == 1)
                @php($route =route('vendor.item.store',['product_gellary' => request()->product_gellary ]))
                @php($product->price = 0)
            @else
                @php($route =route('vendor.item.update', [ isset($temp_product) && $temp_product == 1 ?   $product['item_id'] : $product['id']]))
            @endif

            <input type="hidden" class="route_url" value="{{ $route ?? route('vendor.item.update', [ isset($temp_product) && $temp_product == 1 ?   $product['item_id'] : $product['id']]) }}" >
            <input type="hidden" value="{{$temp_product ?? 0 }}" name="temp_product" >
            <input type="hidden" value="{{$product['id'] ?? null }}" name="item_id" >

                @php($language=\App\Models\BusinessSetting::where('key','language')->first())
                @php($language = $language->value ?? null)
                @php($defaultLang = str_replace('_', '-', app()->getLocale()))
                <div class="row g-2">
                    @if($language)
                    <div class="col-12">
                        <ul class="nav nav-tabs mb-3 border-0">
                            <li class="nav-item">
                                <a class="nav-link lang_link active"
                                href="#"
                                id="default-link">{{translate('messages.default')}}</a>
                            </li>
                            @foreach (json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link"
                                        href="#"
                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <span class="card-header-icon">
                                        <i class="tio-dashboard-outlined"></i>
                                    </span>
                                    <span>{{translate('item_info')}}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($language)
                                <div class="lang_form" id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="default_name">{{translate('messages.name')}} ({{ translate('messages.default') }})</label>
                                        <input type="text" name="name[]" id="default_name" class="form-control" placeholder="{{translate('messages.new_food')}}" value="{{$product->getRawOriginal('name')}}"  >
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group pt-2 mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}} ({{ translate('messages.default') }})</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor min--height-200">{!! $product->getRawOriginal('description') !!}</textarea>
                                    </div>
                                </div>
                                    @foreach(json_decode($language) as $lang)
                                        <?php
                                            if(count($product['translations'])){
                                                $translate = [];
                                                foreach($product['translations'] as $t)
                                                {
                                                    if($t->locale == $lang && $t->key=="name"){
                                                        $translate[$lang]['name'] = $t->value;
                                                    }
                                                    if($t->locale == $lang && $t->key=="description"){
                                                        $translate[$lang]['description'] = $t->value;
                                                    }
                                                }
                                            }
                                        ?>
                                        <div class="d-none lang_form" id="{{$lang}}-form">
                                            <div class="form-group">
                                                <label class="input-label" for="{{$lang}}_name">{{translate('messages.name')}} ({{strtoupper($lang)}})</label>
                                                <input type="text" name="name[]" id="{{$lang}}_name" class="form-control" placeholder="{{translate('messages.new_food')}}" value="{{$translate[$lang]['name']??''}}"  >
                                            </div>
                                            <input type="hidden" name="lang[]" value="{{$lang}}">
                                            <div class="form-group pt-2 mb-0">
                                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}} ({{strtoupper($lang)}})</label>
                                                <textarea type="text" name="description[]" class="form-control ckeditor min--height-200">{!! $translate[$lang]['description']??''!!}</textarea>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                <div id="default-form">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.name')}} ({{ translate('messages.default') }})</label>
                                        <input type="text" name="name[]" class="form-control" placeholder="{{translate('messages.new_food')}}" value="{{$product['name']}}" required>
                                    </div>
                                    <input type="hidden" name="lang[]" value="default">
                                    <div class="form-group pt-2 mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}}</label>
                                        <textarea type="text" name="description[]" class="form-control ckeditor min--height-200">{!! $product['description'] !!}</textarea>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-image"></i>
                                </span>
                                <span>{{translate('item_image')}}</span>
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-auto">
                                <input type="hidden" id="removedImageKeysInput" name="removedImageKeys" value="">

                                <label class="input-label" for="exampleFormControlInput1">{{translate('messages.item_images')}}</label>
                                <div class="row" id="coba">
                                    @foreach ($product->images as $key => $photo)
                                    @php($photo = is_array($photo)?$photo:['img'=>$photo,'storage'=>'public'])
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-6 spartan_item_wrapper" id="product_images_{{ $key }}">
                                            <img class="img--square onerror-image" src="{{\App\CentralLogics\Helpers::get_full_url('product',$photo['img'],$photo['storage'] ?? 'public') }}"
                                            data-onerror-image ="{{asset('/public/assets/admin/img/400x400/img2.jpg')}}" alt="Product image">
                                            <a href="#"  data-key={{ $key }} data-photo="{{ $photo['img'] }}"
                                             class="spartan_remove_row function_remove_img" ><i class="tio-add-to-trash"></i></a>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="text-dark">{{translate('messages.item_thumbnail')}} <small class="text-danger">* ( {{translate('messages.ratio')}} 1:1 )</small></label>
                                <div class="text-center d-block" id="image-viewer-section" class="pt-2">
                                    <img class="img--100 onerror-image" id="viewer"
                                    src="{{ $product['image_full_url'] }}"
                                            data-onerror-image ="{{asset('/public/assets/admin/img/400x400/img2.jpg')}}"
                                            alt="product image"/>
                                </div>
                                <div class="custom-file mt-3">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                            accept=".jpg, .png, .jpeg, .webp , .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose_file')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon">
                                    <i class="tio-dashboard-outlined"></i>
                                </span>
                                <span> {{translate('item_details')}} </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.category')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" id="category-id" class="form-control js-select2-custom get-request"
                                        data-url="{{url('/')}}/store-panel/item/get-categories?parent_id=" data-id="sub-categories"
                                               >
                                            @foreach($categories as $category)
                                                <option
                                                    value="{{$category['id']}}" {{ $category->id==$product_category[0]->id ? 'selected' : ''}} >{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.sub_category')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="sub_category_id" id="sub-categories"
                                                data-id="{{count($product_category)>=2?$product_category[1]->id:''}}"
                                                class="form-control js-select2-custom get-request"
                                                data-url="{{url('/')}}/store-panel/item/get-categories?parent_id=" data-id="sub-sub-categories">

                                        </select>
                                    </div>
                                </div>
                                @if ($module_data['common_condition'])
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="condition_id">{{ translate('messages.Suitable_For') }}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="condition_id" id="condition_id"
                                            data-placeholder="{{ translate('messages.Select_Condition') }}"
                                            id="condition_id" class="js-select2-custom form-control"
                                            oninvalid="this.setCustomValidity('{{ translate('messages.Select_Condition') }}')">
                                            <option value="">---{{translate('messages.select')}}---</option>
                                            @foreach($conditions as $condition)
                                                <option value="{{$condition['id']}}" {{ $product->pharmacy_item_details?->common_condition_id  == $condition['id'] ? 'selected' : '' }}>{{$condition['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                                @if ($module_data['brand'])
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="brand_id">{{ translate('messages.Brand') }}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="brand_id" id="brand_id"
                                            data-placeholder="{{ translate('messages.Select_brand') }}"
                                            id="brand_id" class="js-select2-custom form-control"
                                            oninvalid="this.setCustomValidity('{{ translate('messages.Select_brand') }}')">
                                            <option value="">---{{translate('messages.select')}}---</option>
                                            @foreach($brands as $brand)
                                                <option value="{{$brand['id']}}" {{ $product->ecommerce_item_details?->brand_id  == $brand['id'] ? 'selected' : '' }}>{{$brand['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @endif
                            @if ($module_data['unit'])
                                <div class="col-sm-6 col-lg-4" id="unit_input">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize" for="unit">{{translate('messages.unit')}}</label>
                                        <select name="unit" class="form-control js-select2-custom">
                                            @foreach (\App\Models\Unit::all() as $unit)
                                                <option value="{{$unit->id}}" {{$unit->id == $product->unit_id? 'selected':''}}>{{$unit->unit}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            @if ($module_data['veg_non_veg'])
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.item_type')}}</label>
                                        <select name="veg" class="form-control js-select2-custom">
                                            <option value="0" {{$product['veg']==0?'selected':''}}>{{translate('messages.non_veg')}}</option>
                                            <option value="1" {{$product['veg']==1?'selected':''}}>{{translate('messages.veg')}}</option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                                @if($module_type == 'grocery' || $module_type == 'food')
                                    @if (isset($temp_product) && $temp_product == 1 )
                                        @php($product_nutritions = \App\Models\Nutrition::whereIn('id', json_decode($product?->nutrition_ids))->pluck('id'))
                                        @php($product_allergies = \App\Models\Allergy::whereIn('id', json_decode($product?->allergy_ids))->pluck('id'))
                                    @else
                                        @php($product_nutritions = $product->nutritions->pluck('id'))
                                        @php($product_allergies = $product->allergies->pluck('id'))
                                    @endif

                                    <div class="col-sm-6" id="nutrition">
                                        <label class="input-label" for="sub-categories">
                                            {{translate('Nutrition')}}
                                            <span class="input-label-secondary" title="{{ translate('Specify the necessary keywords relating to energy values for the item.') }}" data-toggle="tooltip">
                                                <i class="tio-info-outined"></i>
                                            </span>
                                        </label>
                                        <select name="nutritions[]" class="form-control multiple-select2" data-placeholder="{{ translate('messages.Type your content and press enter') }}" multiple>
                                            @foreach (\App\Models\Nutrition::all() as $nutrition)
                                                <option value="{{ $nutrition->nutrition }}" {{ $product_nutritions->contains($nutrition->id) ? 'selected' : '' }}>{{ $nutrition->nutrition }}</option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="col-sm-6" id="allergy">
                                        <label class="input-label" for="sub-categories">
                                            {{translate('Allegren Ingredients')}}
                                            <span class="input-label-secondary" title="{{ translate('Specify the ingredients of the item which can make a reaction as an allergen.') }}" data-toggle="tooltip">
                                                <i class="tio-info-outined"></i>
                                            </span>
                                        </label>
                                        <select name="allergies[]" class="form-control multiple-select2" data-placeholder="{{ translate('messages.Type your content and press enter') }}" multiple>
                                            @foreach (\App\Models\Allergy::all() as $allergy)
                                                <option value="{{ $allergy->allergy }}" {{ $product_allergies->contains($allergy->id) ? 'selected' : '' }}>{{ $allergy->allergy }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                                        <input type="number" value="{{$product->price}}" min="0" max="999999999999" name="price"
                                                class="form-control" step="0.01"
                                                placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}}</label>
                                        <input type="number" min="0" value="{{$product['discount']}}" max="100000"
                                                name="discount" class="form-control"
                                                placeholder="{{ translate('messages.Ex:') }} 100">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount_type')}}</label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="percent" {{$product['discount_type']=='percent'?'selected':''}}>
                                                {{translate('messages.percent')}}
                                            </option>
                                            <option value="amount" {{$product['discount_type']=='amount'?'selected':''}}>
                                                {{translate('messages.amount')}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                @if ($module_data['stock'])
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="total_stock">{{translate('messages.total_stock')}}</label>
                                        <input type="number" class="form-control" name="current_stock" min="0" value="{{$product->stock}}" id="quantity">
                                    </div>
                                </div>
                                @endif
                                <div class="col-sm-6 col-lg-4" id="maximum_cart_quantity">
                                    <div class="form-group mb-0">
                                        <label class="input-label"
                                            for="maximum_cart_quantity">{{ translate('messages.Maximum_Purchase_Quantity_Limit') }}
                                            <span
                                            class="input-label-secondary text--title" data-toggle="tooltip"
                                            data-placement="right"
                                            data-original-title="{{ translate('If_this_limit_is_exceeded,_customers_can_not_buy_the_item_in_a_single_purchase.') }}">
                                            <i class="tio-info-outined"></i>
                                        </span>
                                        </label>
                                        <input type="number"  placeholder="{{ translate('messages.Ex:_10') }}" class="form-control" name="maximum_cart_quantity" min="0" value="{{ $product->maximum_cart_quantity }}" id="cart_quantity">
                                    </div>
                                </div>


                                <div class="col-sm-6" id="generic_name">
                                    <label class="input-label" for="sub-categories">
                                        {{translate('generic_name')}}
                                        <span class="input-label-secondary" title="{{ translate('Specify the medicine`s active ingredient that makes it work') }}" data-toggle="tooltip">
                                            <i class="tio-info-outined"></i>
                                        </span>
                                    </label>
                                    <div class="dropdown suggestion_dropdown">
                                        <input type="text" class="form-control" name="generic_name" placeholder="{{ translate('messages.Type your content here') }}" value="{{ isset($temp_product) && $temp_product == 1 ?  \App\Models\GenericName::where('id', json_decode($product?->generic_ids))->first()?->generic_name : $product->generic->pluck('generic_name')->first() }}" autocomplete="off">
                                        @if(count(\App\Models\GenericName::select(['generic_name'])->get())>0)
                                        <div class="dropdown-menu">
                                            @foreach (\App\Models\GenericName::select(['generic_name'])->get() as $generic_name)
                                            <div class="dropdown-item">{{ $generic_name->generic_name }}</div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>


                                <div class="col-sm-6 col-lg-4" id="organic">
                                    <div class="form-check mb-0 p-6">
                                        <input class="form-check-input" name="organic" type="checkbox" value="1" id="flexCheckDefault" {{ $product->organic == 1?'checked':'' }}>
                                        <label class="form-check-label" for="flexCheckDefault">
                                          {{ translate('messages.is_organic') }}
                                        </label>
                                      </div>
                                </div>
                                @if ($module_data['basic'])
                                <div class="col-sm-3 col-lg-3" id="basic">
                                    <div class="form-check mb-0 p-6">
                                        <input class="form-check-input" name="basic" type="checkbox" value="1" id="flexCheckDefaultbasic" {{ $product->pharmacy_item_details?->is_basic == 1?'checked':'' }}>
                                        <label class="form-check-label" for="flexCheckDefaultbasic">
                                          {{ translate('messages.is_basic') }}
                                        </label>
                                      </div>
                                </div>
                                @endif
                                @if ($module_type == 'pharmacy')
                                <div class="col-sm-3 col-lg-3" id="is_prescription_required">
                                    <div class="form-check mb-0 p-6">
                                        <input class="form-check-input" name="is_prescription_required" type="checkbox" value="1" id="flexCheckDefaultPrescription" {{ $product->pharmacy_item_details?->is_prescription_required == 1?'checked':'' }}>
                                        <label class="form-check-label" for="flexCheckDefaultPrescription">
                                          {{ translate('messages.is_prescription_required') }}
                                        </label>
                                      </div>
                                </div>
                                @endif
                                @if ($module_data['halal'])
                                <div class="col-sm-6 col-lg-4" id="halal">
                                    <div class="form-check mb-0 p-6">
                                        <input class="form-check-input" name="is_halal" type="checkbox" value="1" id="flexCheckDefaulthalal" {{ $product->is_halal == 1?'checked':'' }}>
                                        <label class="form-check-label" for="flexCheckDefaulthalal">
                                          {{ translate('messages.is_it_halal') }}
                                        </label>
                                      </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12" id="food_variation_section">
                    <div class="card">
                        <div class="card-header flex-wrap">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-canvas-text"></i>
                                </span>
                                <span>{{ translate('messages.food_variations') }}</span>
                            </h5>
                            <a class="btn text--primary-2" id="add_new_option_button">
                                {{ translate('add_new_variation') }}
                                <i class="tio-add"></i>
                            </a>
                        </div>
                        <div class="card-body">
                            <div id="add_new_option">
                                @if (isset($product->food_variations) && count(json_decode($product->food_variations,true))>0)
                                    @foreach (json_decode($product->food_variations, true) as $key_choice_options => $item)
                                        @if (isset($item['price']))
                                        @break

                                    @else
                                        @include('admin-views.product.partials._new_variations', [
                                            'item' => $item,
                                            'key' => $key_choice_options + 1,
                                        ])
                                    @endif
                                    @endforeach
                                @endif
                            </div>

                                <!-- Empty Variation -->
                                @if (!isset($product->food_variations) || count(json_decode($product->food_variations,true))<1)
                                <div id="empty-variation">
                                    <div class="text-center">
                                        <img src="{{ asset('/public/assets/admin/img/variation.png') }}" alt="">
                                        <div>{{ translate('No variation added') }}</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="attribute_section">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-canvas-text"></i></span>
                                <span>{{translate('attribute')}}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.attribute')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="attribute_id[]" id="choice_attributes"
                                                class="form-control js-select2-custom"
                                                multiple="multiple">
                                            @foreach(\App\Models\Attribute::orderBy('name')->get() as $attribute)
                                                <option
                                                    value="{{$attribute['id']}}" {{in_array($attribute->id,json_decode($product['attributes'],true))?'selected':''}}>{{$attribute['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="customer_choice_options" id="customer_choice_options">
                                        @include('vendor-views.product.partials._choices',['choice_no'=>json_decode($product['attributes']),'choice_options'=>json_decode($product['choice_options'],true)])
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="variant_combination" id="variant_combination">
                                        @include('vendor-views.product.partials._edit-combinations',['combinations'=>json_decode($product['variations'],true),'stock'=>$module_data['stock']])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($module_data['add_on'])
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-puzzle"></i></span>
                                <span>{{translate('addon')}}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.addon')}}<span
                                                class="input-label-secondary"></span></label>
                                        <select name="addon_ids[]" class="form-control js-select2-custom" multiple="multiple">
                                            @foreach(\App\Models\AddOn::where('store_id', \App\CentralLogics\Helpers::get_store_id())->orderBy('name')->get() as $addon)
                                                <option
                                                    value="{{$addon['id']}}" {{in_array($addon->id,json_decode($product['add_ons'],true))?'selected':''}}>{{$addon['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-label"></i></span>
                                <span>{{ translate('tags') }}</span>
                            </h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="row g-2">
                                <div class="col-12">

                                    @if (isset($temp_product) && $temp_product == 1 )
                                    <div class="form-group">
                                        @php($tagids=json_decode($product?->tag_ids) ?? [])
                                        @php( $tags =\App\Models\Tag::whereIn('id',$tagids )->get('tag'))
                                        <input type="text" class="form-control" name="tags" placeholder="Enter tags" value="@foreach($tags as $c) {{$c->tag.','}} @endforeach" data-role="tagsinput">
                                    </div>
                                    @else
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="tags" placeholder="Enter tags" value="@foreach($product->tags as $c) {{$c->tag.','}} @endforeach" data-role="tagsinput">
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($module_data['item_available_time'])
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon"><i class="tio-date-range"></i></span>
                                <span>{{translate('available_time_schedule')}}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row -2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.available_time_starts')}}</label>
                                        <input type="time" value="{{$product['available_time_starts']}}" name="available_time_starts" class="form-control" placeholder="{{ translate('messages.Ex:') }} 10:30 am" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.available_time_ends')}}</label>
                                        <input type="time" value="{{$product['available_time_ends']}}" name="available_time_ends" class="form-control" placeholder="5:45 pm"
                                                required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script')

@endpush

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>
    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script src="{{asset('public/assets/admin')}}/js/view-pages/vendor/product-index.js"></script>
    <script>
        "use strict";

    mod_type="{{ $module_type }}";

    $(document).ready(function() {
        $("#add_new_option_button").click(function(e) {
            $('#empty-variation').hide();
            count++;
            let add_option_view = `
                    <div class="__bg-F8F9FC-card count_div view_new_option mb-2">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <label class="form-check form--check">
                                    <input id="options[` + count + `][required]" name="options[` + count + `][required]" class="form-check-input" type="checkbox">
                                    <span class="form-check-label">{{ translate('Required') }}</span>
                                </label>
                                <div>
                                    <button type="button" class="btn btn-danger btn-sm delete_input_button"
                                        title="{{ translate('Delete') }}">
                                        <i class="tio-add-to-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-xl-4 col-lg-6">
                                    <label for="">{{ translate('name') }}</label>
                                    <input required name=options[` + count +
                `][name] class="form-control new_option_name" type="text" data-count="`+
                count +`">
                                </div>

                                <div class="col-xl-4 col-lg-6">
                                    <div>
                                        <label class="input-label text-capitalize d-flex align-items-center"><span class="line--limit-1">{{ translate('messages.selcetion_type') }} </span>
                                        </label>
                                        <div class="resturant-type-group px-0">
                                            <label class="form-check form--check mr-2 mr-md-4">
                                                <input class="form-check-input show_min_max" data-count="`+count+`" type="radio" value="multi"
                                                name="options[` + count + `][type]" id="type` + count +
                `" checked
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Multiple Selection') }}
                </span>
            </label>

            <label class="form-check form--check mr-2 mr-md-4">
                <input class="form-check-input hide_min_max" data-count="`+count+`" type="radio" value="single"
                    name="options[` + count + `][type]" id="type` + count +
                `"
                                                >
                                                <span class="form-check-label">
                                                    {{ translate('Single Selection') }}
                </span>
            </label>
        </div>
    </div>
</div>
<div class="col-xl-4 col-lg-6">
    <div class="row g-2">
        <div class="col-6">
            <label for="">{{ translate('Min') }}</label>
                                            <input id="min_max1_` + count + `" required  name="options[` + count + `][min]" class="form-control" type="number" min="1">
                                        </div>
                                        <div class="col-6">
                                            <label for="">{{ translate('Max') }}</label>
                                            <input id="min_max2_` + count + `"   required name="options[` + count + `][max]" class="form-control" type="number" min="1">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="option_price_` + count + `" >
                                <div class="bg-white border rounded p-3 pb-0 mt-3">
                                    <div  id="option_price_view_` + count + `">
                                        <div class="row g-3 add_new_view_row_class mb-3">
                                            <div class="col-md-4 col-sm-6">
                                                <label for="">{{ translate('Option_name') }}</label>
                                                <input class="form-control" required type="text" name="options[` +
                count +
                `][values][0][label]" id="">
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <label for="">{{ translate('Additional_price') }}</label>
                                                <input class="form-control" required type="number" min="0" step="0.01" name="options[` +
                count + `][values][0][optionPrice]" id="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3 p-3 mr-1 d-flex "  id="add_new_button_` + count +
                `">
                                        <button type="button" class="btn btn--primary btn-outline-primary add_new_row_button" data-count="`+
                count +`">{{ translate('Add_New_Option') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

            $("#add_new_option").append(add_option_view);
        });
    });

    function add_new_row_button(data) {
        countRow = 1 + $('#option_price_view_' + data).children('.add_new_view_row_class').length;
        let add_new_row_view = `
            <div class="row add_new_view_row_class mb-3 position-relative pt-3 pt-sm-0">
                <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Option_name') }}</label>
                        <input class="form-control" required type="text" name="options[` + data + `][values][` +
            countRow + `][label]" id="">
                    </div>
                    <div class="col-md-4 col-sm-5">
                        <label for="">{{ translate('Additional_price') }}</label>
                        <input class="form-control"  required type="number" min="0" step="0.01" name="options[` + data +
            `][values][` + countRow + `][optionPrice]" id="">
                    </div>
                    <div class="col-sm-2 max-sm-absolute">
                        <label class="d-none d-sm-block">&nbsp;</label>
                        <div class="mt-1">
                            <button type="button" class="btn btn-danger btn-sm deleteRow"
                                title="{{ translate('Delete') }}">
                                <i class="tio-add-to-trash"></i>
                            </button>
                        </div>
                </div>
            </div>`;
        $('#option_price_view_' + data).append(add_new_row_view);

    }



        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                let sub_category = '{{count($product_category)>=2?$product_category[1]->id:''}}';
                let sub_sub_category ='{{count($product_category)>=3?$product_category[2]->id:''}}';
                getRequest('{{url('/')}}/store-panel/item/get-categories?parent_id=' + category + '&&sub_category=' + sub_category, 'sub-categories');
                getRequest('{{url('/')}}/store-panel/item/get-categories?parent_id=' + sub_category + '&&sub_category=' + sub_sub_category, 'sub-sub-categories');
            }, 1000)
        });





    function add_more_customer_choice_option(i, name) {
        let n = name;

        $('#customer_choice_options').append(
            `<div class="__choos-item"><div><input type="hidden" name="choice_no[]" value="${i}"><input type="text" class="form-control d-none" name="choice[]" value="${n}" placeholder="{{ translate('messages.choice_title') }}" readonly> <label class="form-label">${n}</label> </div><div><input type="text" class="form-control combination_update" name="choice_options_${i}[]" placeholder="{{ translate('messages.enter_choice_values') }}" data-role="tagsinput"></div></div>`
        );
        $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
    }



        function combination_update() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('vendor.item.variant-combination')}}',
                data: $('#product_form').serialize()+'&stock={{$module_data['stock']}}',
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    $('#variant_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        $('#product_form').on('submit', function () {
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: $('.route_url').val() ,
                data: $('#product_form').serialize(),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#loading').hide();
                    if (data.errors) {
                        for (let i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    }
                    if(data.product_approval){
                            toastr.success(data.product_approval, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('vendor.item.pending_item_list')}}';
                        }, 2000);
                    }
                    if(data.success) {
                        toastr.success(data.success, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('vendor.item.list')}}';
                        }, 2000);
                    }
                }
            });
        });

        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'item_images[]',
                maxCount: 6,
                rowHeight: '120px',
                groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: "{{asset('public/assets/admin/img/400x400/img2.jpg')}}",
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error("{{translate('messages.please_only_input_png_or_jpg_type_file')}}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error("{{translate('messages.file_size_too_big')}}", {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
        // $('#product_form').on('keydown', function(e) {
        //     if (e.key === 'Enter') {
        //     e.preventDefault(); // Prevent submission on Enter
        //     }
        // });





    </script>
@endpush


