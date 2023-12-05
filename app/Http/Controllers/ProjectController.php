<?php

namespace App\Http\Controllers;

use App;
use App\Project;
use App\Product;
use App\User;
use App\Category;
use App\Files;
use App\SeoSettings;
use App\CurrenciesCb;
use App\Page;
use App\Location;
use App\Nikita;
use http\Exception\BadUrlException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Auth;
use mysql_xdevapi\Exception;
// use Validator;
use App\RequiredProject;
use App\Place;
use Illuminate\Support\Facades\Validator;

// '<a class="item-in-baloon" href="{{ route('announcement.show', [app()->getLocale(), $announcement->code]) }}" target="_blank"><img src="{{ $announcement->thumbnail ?? (url('/storage/no_image.jpg')) }}" alt="No image"><span class="ann-code"><span>{{ $announcement->property_type->title }}</span><span>{{ $announcement->code }}</span></span><p class="baloon-item-address">{{ $announcement->place->parent->name.','.$announcement->place->name }}</p></a>'
class ProjectController extends Controller
{

    /**
     * @return $response of the api request
     * @var $request instance of Request
     *
     */
    public function nikitaSend(Request $request)
    {
        $nikita = new Nikita;
        $recipient = '37493122744';
        $id = 1;
        $message = '"' . 'Ձեր հարցմանը համապատասխան գույքեր կարող եք տեսնել հետևյալ հղումով՝ ' . 'https://redgroup.webapricot.am/properties/' . $id . '"';

        $response = $nikita->send($recipient, $message);
        return $response;
    }


    // generator uploaded image to small size
    public function generatorSmallUrl(){


           $files = Files::get();

           foreach ($files as $key => $file ){
               $filename_path_small = explode("/",$file->url);


               if(array_key_exists(2,$filename_path_small) && $filename_path_small[2] == "apireal.webapricot.am"){
                   //   dd($filename_path_small);
                   echo $file->url;
                   echo"---------";
                   $file->url = str_replace('http://apireal.webapricot.am',"https://api.redgroup.am",$file->url);
                   echo $file->url;
                   echo"+++++++++";
                   $file->save();
//                   $filename_path_small[count($filename_path_small) -1] = "small".$filename_path_small[count($filename_path_small) -1];
//                   $filename_path = implode("/",$filename_path_small);
//
//                   $filename = $file->image_url;
//                   $file_headers = @get_headers($filename);
//
//                       if ($file_headers[0] == 'HTTP/1.1 404 Not Found'){
//
//                       } else {
//                           $img = file_get_contents($filename);
//                           $decoded = base64_encode($img);
//                           $smallImage = $this->resize_image($img, 210, 120);
//                           if (!is_dir(dirname($filename_path))) {
//                               mkdir(dirname($filename_path), 0775, true);
//                           }
//
//                           file_put_contents(str_replace('https://www.redgroup.am',"/var/www/clients/laravel/redgroup.am/public",$filename_path), $smallImage);
////                           $fs = Files::where('id',$file->id)->update(['url_small' => ]);
//                           $file->small_image_url = $filename_path;
//
//                           $file->save();
//
//                       }
//


                   }else{

               }



               }


           }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locale = session('lang') ?? app()->getLocale();
        $projectsAllCoordinates = Project::where('visibility', 1)->with(['file','main_project'])->orderBy('top', 'DESC')->orderBy('status', 'ASC')->orderBy('project_id', 'DESC')->get(['coordinates']);
        $projects = Project::where('visibility', 1)->with(['file','main_project'])->orderBy('top', 'DESC')->orderBy('status', 'ASC')->orderBy('project_id', 'DESC')->paginate(6);
        $categories = Category::with('project')->get();
        $favs = json_decode($request->cookie('favs'), true);
        $locations = Location::where('locale', session('lang'))->with('childs', 'parent')->get();
        $project_new_days = App\Setting::where('name', 'project_new_days')->value("value");
        $states = Place::whereHas('type', function ($query) use ($locale) {
            $query->where([['locale', $locale], ['parent_id', 2]]);
        })->with('children.children')->get();
        if (isset($request->mode)) {

            return response()->json(['projects' => $projects, 'project_new_days' => $project_new_days]);
        } else {
            return view('pages.projects.archive')->with(['projects' => $projects,'projectsAllCoordinates'=> $projectsAllCoordinates, 'states' => $states, 'categories' => $categories, 'favs' => $favs, 'locations' => $locations, 'project_new_days' => $project_new_days]);
        }
    }

    /**
     * Display a listing of the resource in admin page.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAdmin()
    {
        $projects = Project::orderBy('created_at', 'DESC')->get();
        $categories = Category::all();
        $pages = Page::get();

        return view('admin.projects.archive')->with(['projects' => $projects, 'categories' => $categories, 'pages' => $pages]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Show the form for creating a new resourcefrom admin panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function createProjectAdmin($project_id, Request $request)
    {
        $categories = Category::all();
        $locale = $_GET['locale'] ?? session('lang');
        $locations = Location::where(['locale' => $locale])->with('childs', 'parent')->get();
        if ($project_id != 0) {
            $project = Project::find((int)$project_id);
            $project_arm = Project::where('project_id',(int)$project_id)->where('locale','hy')->withoutGlobalScopes()->first();
            return view('admin.projects.default')->with(['categories' => $categories,'locale' => $locale,'project_arm' => $project_arm, 'project_id' => $project_id, 'project' => $project, 'locations' => $locations]);
        }
        // $this->changeLang($locale);
        return view('admin.projects.default')->with(['categories' => $categories, 'project_id' => $project_id, 'locations' => $locations]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeSingleAdmin(Request $request)
    {
        $validations = [
            'currency_single' => 'required',
        ];
        $data = $request->data;
        $options = array();
        parse_str($request->options, $options);
        isset($request->slug) ? $options['slug'] = $request->slug : "";
        if($request->currency_single == 'rate_not_from_kb'){
            $validations['currency_usd'] = 'required';
            $validations['currency_eur'] = 'required';
            $validations['currency_rub'] = 'required';
        }
        if (!(isset($options['project_id']) && $options['project_id'] != 0)) {
            $validations['slug'] = 'required|unique:projects,slug';
        }
        
        $validator = Validator::make($request->all(),$validations);
        if (isset($options['project_id']) && $options['project_id'] != 0) {
                $validator->after(function ($validator) use ($options) {
                    if (Project::where('project_id','!=',$options['project_id'])->where('slug',$options['slug'])->exists()) {
                        $validator->errors()->add('slug', 'Slug already taken');
                    }
                }
            );
        }
        if ($validator->fails()) {
            return response()->json(['status'=>false]);
        } else{
            $fields = array();
            $fields['project_id'] = 12;
            $fields['locale'] = isset($request->lang) ? $request->lang : session('lang');
            $fields['title'] = $data['contentTitle'];
            if (isset($options['location_id'])) {
                $fields['location_id'] = $options['location_id'];
            }
            $fields['property_type_id'] = isset($options['property_type_id']) ? $options['property_type_id'] : null;
            $fields['address'] = $options['address'];
            $fields['coordinates'] = $options['coordinates'];
            $fields['slug'] = ($options['slug'] != '') ? str_replace(' ', '-', $options['slug']) : str_replace(' ', '-', $data['contentTitle']);
            $fields['phone'] = $options['phone'];
            $fields['short_name'] = $options['short_name'];
            unset($data['contentTitle']);
            $data['description'] = str_replace(['amp;', '&lt;', '&gt;', '\r\n'], ['', '<', '>', ''], $data['description']);
            $fields['content'] = json_encode($data);
            $fields['visibility'] = $request->visibility;
            $fields['get_rate_type'] = $request->currency_single;
            $fields['currency_usd'] = $request->currency_usd;
            $fields['currency_eur'] = $request->currency_eur;
            $fields['currency_rub'] = $request->currency_rub;

    
            $project = new Project;
            $project_stored = [];
            if (isset($options['project_id']) && $options['project_id'] != 0) {
                $parent_project = Project::withoutGlobalScopes()->where('id', $options['project_id'])->first();
    //			    $bottom_block_images = $parent_project->files()->where('block_id', 'bottom_gallery_img')->get(['url', 'alt', 'block_id']);
                $fields['location_id'] = $parent_project->location_id;
                $fields['featured_image'] = $parent_project->featured_image;
                $fields['project_id'] = $options['project_id'];
                $fields['top'] = Project::where('project_id', $options['project_id'])->where('locale', 'hy')->withoutGlobalScopes()->value('top');
                $project_stored = $project->fill($fields);
                $project->save();
    
    //				$images = json_decode($request->images, true);
    //				$imgStored = $this->storeImages($images, $project->id);
    
    //				$products = json_decode($request->products, true);
    //				$prodStored = $this->storeProducts($products, $project->id)['all'];
            }
            else {
                $fields['top'] = $request->top;
                $project_stored = $project->fill($fields);
                $project->save();
                $project->update(['project_id' => $project_stored['id']]);
    
                $images = json_decode($request->images, true);
                $imgStored = $this->storeImages($images, $project_stored['id']);
                Files::insert($imgStored);
    
                $products = json_decode($request->products, true);
                $prodStored = $this->storeProducts($products, $project->id)['all'];
            }
    
            if (isset($options['category_id'])) {
                $project->category()->sync($options['category_id']);
            }
    
            // Product::insert($prodStored);
            if (isset($request->featured_image)) {
                $filename = microtime();
                $url = '/storage/projects/' . $project_stored['id'] . '/' . $filename;
                $filename_path = storage_path('app/public/projects/' . $project_stored['id'] . '/' . $filename);
    
                $base64Url = $request->featured_image;
                $data = explode(',', $base64Url);
                $decoded = base64_decode($data[1]);
                if (!is_dir(dirname($filename_path))) {
                    mkdir(dirname($filename_path), 0775, true);
                }
                file_put_contents($filename_path, $decoded);
                $project->featured_image = url($url);
                $project->save();
            }
            if (isset($options['seo_value'])) {
                $seo_settings = array();
                foreach ($options['seo_value'] as $key => $value) {
                    $seo_settings[] = [
                        'post_id' => $project_stored['id'],
                        'seo_key' => $key,
                        'seo_value' => $value
                    ];
                }
                SeoSettings::where('post_id', $project_stored['id'])->delete();
                SeoSettings::insert($seo_settings);
            }
            $redirect_url = route('admin.projects.singleadmin', $fields['slug']);
            // session(['lang' => $request->locale]);
            // app()->setLocale(session('lang'));
    
            return response()->json(['status'=>true,'redirect_url'=>$redirect_url]);
        }

    }

    /**
     * @param array $images
     * @param $project_stored_id
     * @return array
     */
    private function storeImages(array $images, $project_stored_id)
    {
        $imgStored = array();
        foreach ($images as $image) {
            if (array_key_exists('url', $image)) {
                $imgStored[] = array(
                    'url' => $image['url'],
                    'url_small' => isset($image['url_small']) && count((array)$image['url_small']) > 0 ? $image['url_small'] : null,
                    'alt' => isset($image['alt']) ? $image['alt'] : '',
                    'title' => isset($image['title']) ? $image['title'] : '',
                    'block_id' => $image['block_id'],
                    'post_id' => $project_stored_id
                );
            } else {
                $imageNameArray= explode(".",$image['name']);
                $index = count($imageNameArray) - 1;
                $imageFormat = $imageNameArray[$index];
                $filename = microtime() .'.'. $imageFormat;
                $url = '/storage/projects/' . $project_stored_id . '/' . $filename;
                $url_small = '/storage/projects/' . $project_stored_id . '/small' . $filename;
                $filename_path = storage_path('app/public/projects/' . $project_stored_id . '/' . $filename);
                $filename_path_small = storage_path('app/public/projects/' . $project_stored_id . '/small' . $filename);

                $base64Url = $image['img64'];
                $data = explode(',', $base64Url);
                $decoded = base64_decode($data[1]);
                if (!is_dir(dirname($filename_path))) {
                    mkdir(dirname($filename_path), 0775, true);
                }
                file_put_contents($filename_path, $decoded);

                $smallImage = $this->resize_image($decoded, 140, 80);

                file_put_contents($filename_path_small, $smallImage);

                $imgStored[] = array(
                    'url' => url($url),
                    'url_small' => url($url_small),
                    'alt' => $image['alt'],
                    'title' =>  '',
                    'block_id' => $image['block_id'],
                    'post_id' => $project_stored_id
                );
            }
        }

        return $imgStored;
    }


    function resize_image($file, $w, $h, $crop=FALSE) {
        //list($width, $height) = getimagesize($file);
        $src = imagecreatefromstring($file);
        if (!$src) return false;
        $width = imagesx($src);
        $height = imagesy($src);

        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        //$src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
        $whiteBackground = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst,0,0,$whiteBackground); // fill the background with white
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        // Buffering
        ob_start();
        imagepng($dst);
        $data = ob_get_contents();
        ob_end_clean();
        return $data;
    }

    public function adminChangeImage(Request $request)
    {

        if ($request->hasFile('image')) {
            $product = Product::find($request->get('product_id'));
            $image = $request->image;
            $imageName = microtime() . ".jpg";
            $image->move(storage_path('app/public/projects/' . $product->post_id ), $imageName);
            $url = url('/') . '/storage/projects/' . $product->post_id . '/' . $imageName;
            $urlSmall = url('/') . '/storage/projects/' . $product->post_id . '/small' . $imageName;
            $product->image_url = $url;
            $img = file_get_contents(str_replace(" ","%20",$url));
            $smallImage = $this->resize_image($img, 210, 120);

            $filename_path_small = storage_path('app/public/projects/' . $product->post_id . '/small' . $imageName);

            file_put_contents($filename_path_small, $smallImage);
            $product->small_image_url = $urlSmall;
            $product->save();

            return response()->json(['success' => 1,'product_id' => $request->get('product_id'),'url' => $url]);

        }
        return response()->json(['success' => 0]);

    }


    private function storeProducts(array $products, $project_stored_id, $deleted_products = [])
    {
        $prodStored = array();
        $existing_urls = array();
        if (!empty($deleted_products)) {
            Product::destroy($deleted_products);
        }
        $project_status_change = true;
        foreach ($products as $prod) {
            if (array_key_exists('image_url', $prod)) {
                $prodStored[] = array(
                    'title' => str_replace('  ', ' ', str_replace('  ', ' ', isset($prod['title']) ? $prod['title'] : '')),
                    'post_id' => $project_stored_id,
                    'price' => isset($prod['price']) && $prod['price'] > 0 ? $prod['price'] : 0,
                    'currency_id' => $prod['currency_id'],
                    'image_url' => $prod['image_url'],
                    'status' => $prod['status'],
                    'top' => !empty($prod['top']) ? $prod['top'] : 0,
                    'short_desc' => !empty($prod['short_desc']) ? $prod['short_desc'] : '',
                    'discount' => !empty($prod['discount']) ? $prod['discount'] : null,
                    'rooms' => !empty($prod['rooms']) ? $prod['rooms'] : null,
                    'block' => !empty($prod['block']) ? $prod['block'] : null,
                    'entry' => !empty($prod['entry']) ? $prod['entry'] : null,
                    'floor' => !empty($prod['floor']) ? $prod['floor'] : null,
                    'area' => !empty($prod['area']) ? $prod['area'] : null,
                    'property_type_id' => !empty($prod['property_type']) ? $prod['property_type'] : null
                );
                array_push($existing_urls, $prod['image_url']);
                Product::where('id', $prod['id'])->update(array(
                    'title' => str_replace('  ', ' ', str_replace('  ', ' ', isset($prod['title']) ? $prod['title'] : '')),
                    'post_id' => $project_stored_id,
                    'price' => isset($prod['price']) && $prod['price'] > 0 ? $prod['price'] : 0,
                    'currency_id' => $prod['currency_id'],
                    'image_url' => $prod['image_url'],
                    'status' => $prod['status'],
                    'top' => !empty($prod['top']) ? $prod['top'] : 0,
                    'short_desc' => !empty($prod['short_desc']) ? $prod['short_desc'] : '',
                    'discount' => !empty($prod['discount']) ? $prod['discount'] : null,
                    'rooms' => !empty($prod['rooms']) ? $prod['rooms'] : null,
                    'block' => !empty($prod['block']) ? $prod['block'] : null,
                    'entry' => !empty($prod['entry']) ? $prod['entry'] : null,
                    'floor' => !empty($prod['floor']) ? $prod['floor'] : null,
                    'area' => !empty($prod['area']) ? $prod['area'] : null,
                    'property_type_id' => !empty($prod['property_type']) ? $prod['property_type'] : null
                ));
                // }
            } else {
                $filename = microtime() . $prod['name'];
                $url = '/storage/projects/' . $project_stored_id . '/' . $filename;
                $small_image_url = '/storage/projects/' . $project_stored_id . '/small' . $filename;
                $filename_path = storage_path('app/public/projects/' . $project_stored_id . '/' . $filename);
                $filename_path_small = storage_path('app/public/projects/' . $project_stored_id . '/small' . $filename);

                $base64Url = $prod['img64'];
                $data = explode(',', $base64Url);
                $decoded = base64_decode($data[1]);
                if (!is_dir(dirname($filename_path))) {
                    mkdir(dirname($filename_path), 0775, true);
                }
                file_put_contents($filename_path, $decoded);
                $smallImage = $this->resize_image($decoded, 210, 120);
                file_put_contents($filename_path_small, $smallImage);
                $prodStored[] = array(
                    'title' => isset($prod['title']) ? $prod['title'] : '',
                    'post_id' => $project_stored_id,
                    'price' => isset($prod['price']) && $prod['price'] > 0 ? $prod['price'] : 0,
                    'currency_id' => $prod['currency_id'],
                    'image_url' => url($url),
                    'small_image_url' => url($small_image_url),
                    'status' => $prod['status'],
                    'top' => !empty($prod['top']) ? $prod['top'] : 0,
                    'short_desc' => !empty($prod['short_desc']) ? $prod['short_desc'] : '',
                    'discount' => !empty($prod['discount']) ? $prod['discount'] : null,
                    'rooms' => !empty($prod['rooms']) ? $prod['rooms'] : null,
                    'block' => !empty($prod['block']) ? $prod['block'] : null,
                    'entry' => !empty($prod['entry']) ? $prod['entry'] : null,
                    'floor' => !empty($prod['floor']) ? $prod['floor'] : null,
                    'area' => !empty($prod['area']) ? $prod['area'] : null,
                    'property_type_id' => !empty($prod['property_type']) ? $prod['property_type'] : null
                );
                $new_product_id = Product::insertGetId(array(
                    'title' => isset($prod['title']) ? $prod['title'] : '',
                    'post_id' => $project_stored_id,
                    'price' => isset($prod['price']) && $prod['price'] > 0 ? $prod['price'] : 0,
                    'currency_id' => $prod['currency_id'],
                    'image_url' => url($url),
                    'small_image_url' => url($small_image_url),
                    'status' => $prod['status'],
                    'discount' => !empty($prod['discount']) ? $prod['discount'] : null,
                    'rooms' => !empty($prod['rooms']) ? $prod['rooms'] : null,
                    'block' => !empty($prod['block']) ? $prod['block'] : null,
                    'entry' => !empty($prod['entry']) ? $prod['entry'] : null,
                    'floor' => !empty($prod['floor']) ? $prod['floor'] : null,
                    'area' => !empty($prod['area']) ? $prod['area'] : null,
                    'top' => !empty($prod['top']) ? $prod['top'] : 0,
                    'short_desc' => !empty($prod['short_desc']) ? $prod['short_desc'] : '',
                    'property_type_id' => !empty($prod['property_type']) ? $prod['property_type'] : null
                ));
                $onlyNewProd[] = array(
                    'title' => isset($prod['title']) ? $prod['title'] : '',
                    'post_id' => $new_product_id,
                    'price' => isset($prod['price']) && $prod['price'] > 0 ? $prod['price'] : 0,
                    'currency_id' => $prod['currency_id'],
                    'image_url' => url($url),
                    'small_image_url' => url($small_image_url),
                    'top' => !empty($prod['top']) ? $prod['top'] : 0,
                    'short_desc' => !empty($prod['short_desc']) ? $prod['short_desc'] : '',
                    'discount' => !empty($prod['discount']) ? $prod['discount'] : null,
                    'rooms' => !empty($prod['rooms']) ? $prod['rooms'] : null,
                    'block' => !empty($prod['block']) ? $prod['block'] : null,
                    'entry' => !empty($prod['entry']) ? $prod['entry'] : null,
                    'floor' => !empty($prod['floor']) ? $prod['floor'] : null,
                    'area' => !empty($prod['area']) ? $prod['area'] : null,
                    'property_type_id' => !empty($prod['property_type']) ? $prod['property_type'] : null
                );
            }
            if ($prod['status'] != 'sold') {
                $project_status_change = false;
            }
        }
        $onlyNewProd = isset($onlyNewProd) ? array_values($onlyNewProd) : [];
        return array('all' => $prodStored, 'only_new' => $onlyNewProd, 'project_status_change' => $project_status_change);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function showArchive(Project $project)
    {

    }


    /**
     * Display single project.
     * @param string $slug
     * @return \Illuminate\Http\Response
     */
    public function showSingle($slug, Request $request)
    {

        $single = '';
        $locale = session('lang') ?? app()->getLocale();
        if ($request->has('id')) {
            $single = Product::findOrFail($request->id);
        }
        $hidden_new_request_button = true;
        $locations = Location::where('locale', session('lang'))->with('childs', 'parent')->get();

        $project = Project::withoutGlobalScopes()->where('slug', $slug)->where('locale', session('lang'))->first();
        $currencies_cb = CurrenciesCb::first();
        if ($project) {
            Project::withoutGlobalScopes()->where('project_id', $project->project_id)->update([
                'viewed' => DB::raw('viewed + 1'),
            ]);
            $project->content = json_decode($project->content, true);
            $categories = Category::all();
            $slugs = $this->getPostTranslation($project);
            $all_products = $project->products()->where('status', '!=', 'sold')
                ->orderBy('top', 'desc')
                ->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')
                ->orderBy('block', 'ASC')
                ->orderBy('entry', 'ASC')
                ->orderBy('floor', 'ASC')
                ->orderBy('rooms', 'ASC');
            $all_products_count = $all_products->get()->count();
            $products = $all_products->take(9)->get();
            $all_products_count = $all_products->count();
            $favs = json_decode($request->cookie('favs'), true);
            $bottom_block_img = $project->file()->where('block_id', 'bottom_gallery_block')->paginate(20);

            $currency_rate = $request->has('currency_rate')?$request->currency_rate:'amd';
            $products_prices = $this->getProductsPrices($products,$currency_rate);
            $product_currency = $request->has('currency_rate')?strtoupper($request->currency_rate):'AMD';

            $states = Place::whereHas('type', function ($query) use ($locale) {
                $query->where([['locale', $locale], ['parent_id', 2]]);
            })->with('children.children')->get();
            
            return view('pages.projects.single', compact('project', 'categories', 'slugs', 'states', 'bottom_block_img', 'products', 'products_prices','all_products_count', 'favs', 'single', 'locations', 'hidden_new_request_button'));
        } else {
            return redirect(route('projects.archive'));
        }
    }

    public function loadMore(Request $request)
    {
        $projects = Project::where('visibility', 1)->with(['file','main_project'])->orderBy('top', 'DESC')->orderBy('status', 'ASC')->orderBy('project_id', 'DESC')->paginate(6);
        return response()->json($projects);
    }

    public function products(Request $request)
    {

        $project = Project::find($request->project_id);
        $filter_rooms = [];
        $rooms = [];
        parse_str($request->rooms, $filter_rooms);
        if (isset($filter_rooms['rooms'])) {
            foreach ($filter_rooms['rooms'] as $index => $room) {
                if (substr($room, -1) == '+') {
                    for ($i = (int)substr($room, 0, 1); $i <= ((int)substr($room, 0, 1) + 5); $i++) {
                        array_push($rooms, $i);
                    }
                } else {
                    array_push($rooms, intval($room));
                }
            }
        }
        if ($request->hide_sold == 'true') {
            $products = $project->products()->where('status', '!=', 'sold')->with('currency');
        } else {
            $products = $project->products()->with('currency');
        }
        if (!empty($rooms)) {
            $products = $products->whereIn('rooms', $rooms);
        }
        
        $all_products_count = $products->get()->count();
        $all_products = $products->with('project:project_id,short_name')
            ->orderBy('top', 'desc')
            ->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')
            ->orderBy('block', 'ASC')
            ->orderBy('entry', 'ASC')
            ->orderBy('floor', 'ASC')
            ->orderBy('rooms', 'ASC')
            ->offset($request->offset)->take(9)->get();

        if (in_array($project->id, config('app.hidden_price_projects'))) {
            $all_products = $all_products->map(function($product) {
                unset($product->price);
                unset($product->discount);
                return $product;
            });
        }
        $currency_rate = $request->has('currency_rate')?$request->currency_rate:'amd';
        $products_prices = !in_array($project->project_id, config('app.hidden_price_projects')) ? $this->getProductsPrices($all_products,$currency_rate) : [];
        $product_currency = $request->has('currency_rate')?strtoupper($request->currency_rate):'AMD';
        return response()->json(['data' => $all_products, 'total' => $all_products_count,'products_prices' =>$products_prices,'products_currency'=>$product_currency]);
    }

    public function getBottomImg($locale, $project_id)
    {
        $project = new Project;
        $bottom_block_img = $project::find($project_id)->file()->where('block_id', 'bottom_gallery_block')->paginate(2);
        $imgs = array();
        foreach ($bottom_block_img as $img) {
            $imgs[] = $img->url;
        }
        return response()->json($imgs);
    }

    public function getPostTranslation(Project $project)
    {
        $slugs = array();
        $projects = Project::where('project_id', $project->project_id)->get();
        foreach ($projects as $project) {
            $slugs[$project->locale] = $project->slug;
        }
        return $slugs;
    }

    public function showAdminSingle($slug)
    {
        $project = Project::where('slug', $slug)->first();
        if (!$project) {
            $project_id = DB::table('projects')->where('slug', $slug)->first();
            if ($project_id) {
                $project = Project::where('project_id', $project_id->id)->where('locale', session('lang'))->first();
                if (!$project) {
                    return redirect(route('admin.projects.single.default', 0));
                }
            } else {
                return redirect(route('admin.projects.single.default', 0));
            }
        }
        $project_arm = Project::where('project_id',$project['project_id'])->where('locale','hy')->withoutGlobalScopes()->first();
        $project->content = json_decode($project->content, true);
        $categories = Category::all();
        $langs = DB::table('projects')->where('project_id', $project->project_id)->pluck('locale')->toArray();
        $seo = array();
        if (isset($project->seo_settings)) {
            foreach ($project->seo_settings as $setting) {
                $seo[$setting->seo_key] = $setting->seo_value;
            }
        }
        $slugs = $this->getPostTranslation($project);
        $pages = Page::get();
        $locations = Location::where(['locale' => session('lang')])->with('childs', 'parent')->get();
        return view('admin.projects.single')->with(['project' => $project, 'categories' => $categories, 'seo' => $seo, 'slugs' => $slugs, 'project_arm' => $project_arm, 'pages' => $pages, 'langs' => $langs, 'locations' => $locations]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateProject(Request $request)
    {
        $project = Project::find($request->id);
        $validations = [
            'currency_single' => 'required',
        ];
        $options = array();
        if($request->currency_single == 'rate_not_from_kb'){
            $validations['currency_usd'] = 'required';
            $validations['currency_eur'] = 'required';
            $validations['currency_rub'] = 'required';
        }
        $validator = Validator::make($request->all(),$validations);
        if ($project) {
            parse_str($request->options, $options);
            if (isset($options['category_id'])) {
                $project->category()->sync($options['category_id']);
            }
            if (!isset($request->slug)) {
                $request->merge(['slug' => $options['slug']]);
            }
            $validator->after(function ($validator) use ($project,$request) {
                    if (Project::where('project_id','!=',$project['project_id'])->where('slug',$request->slug)->exists()) {
                        $validator->errors()->add('slug', 'Slug already taken');
                    }
                }
            );
            // $validations['slug'] = 'unique:projects,slug,NULL,id,project_id,NOT '.$project['project_id'];
        }
        if ($validator->fails()) {
            return response()->json(['status'=>false]);
        } else{
            $data = $request->data;
            
            $images = json_decode($request->images, true);
            $products = json_decode($request->products, true);

            $fields = array();
            $fields['locale'] = session('lang');
            $fields['title'] = html_entity_decode($data['contentTitle']);
            $fields['property_type_id'] = isset($options['property_type_id']) ? $options['property_type_id'] : null;
            $fields['location_id'] = $options['location_id'];
            $fields['address'] = $options['address'];
            $fields['coordinates'] = $options['coordinates'];
            $fields['slug'] = $options['slug'] ?? $request->slug;
            $fields['phone'] = $options['phone'];
            $fields['short_name'] = $options['short_name'];
            unset($data['contentTitle']);
            $data['description'] = str_replace(['amp;', '&lt;', '&gt;', '\r\n'], ['', '<', '>', ''], $data['description']);
            $fields['content'] = json_encode($data);
            $fields['visibility'] = $request->visibility;
            $fields['get_rate_type'] = $request->currency_single;
            $fields['currency_usd'] = $request->currency_usd;
            $fields['currency_eur'] = $request->currency_eur;
            $fields['currency_rub'] = $request->currency_rub;

            if (isset($request->featured_image)) {
                if ($request->featured_image !== 'set') {
                    $filename = time() . 'jpg';
                    $url = '/storage/projects/' . $project->id . '/' . $filename;
                    $filename_path = storage_path('app/public/projects/' . $project->id . '/' . $filename);

                    $base64Url = $request->featured_image;
                    $data = explode(',', $base64Url);
                    $decoded = base64_decode($data[1]);
                    // return response()->json($data);
                    if (!is_dir(dirname($filename_path))) {
                        mkdir(dirname($filename_path), 0775, true);
                    }
                    file_put_contents($filename_path, $decoded);
                    $fields['featured_image'] = url($url);
                }
            }

            $project->fill($fields);
            $project->save();
            Project::where('project_id',$project['project_id'])->withoutGlobalScopes()->update(['slug' => ($options['slug'] ?? $request->slug)]);

            $imgStored = $this->storeImages($images, $request->id);
            Files::where('post_id', $request->id)->delete();
            Files::insert($imgStored);
            if (session('lang') == 'hy') {
                Project::where('project_id', $project->project_id)->withoutGlobalScopes()->update(['top' => $request->top]);
                $allProdStored = $this->storeProducts($products, $request->id, $request->deleted_products);
                $prodStored = $allProdStored['all'];
                if ($allProdStored['project_status_change']) {
                    $project->status = 'sold';
                    Project::where('project_id', $project->project_id)->withoutGlobalScopes()->update(['status' => 'sold']);
                } else {
                    $project->status = null;
                }
                $project->save();


                if (isset($options['seo_value'])) {
                    $seo_settings = array();
                    foreach ($options['seo_value'] as $key => $value) {
                        $seo_settings[] = [
                            'post_id' => $request->id,
                            'seo_key' => $key,
                            'seo_value' => $value
                        ];
                    }
                    SeoSettings::where('post_id', $request->id)->delete();
                    SeoSettings::insert($seo_settings);
                }
                $new_products = $allProdStored['only_new'];
                return response()->json(['status'=>true,$new_products]);
            }
            return response()->json(['status' => 1]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Project $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        if ($project->locale == 'hy') {
            $project->file()->delete();
            $project->products()->delete();
            $project->seo_settings()->delete();
            Project::where('project_id', $project->id)->delete();
        } else {
            $project->delete();
        }

        return redirect(route('admin.projects.archive'));
    }

    public function productPaginate(Request $request)
    {
        $offset = $request->offset;
    }

    public function changeLang($lang)
    {
        if (isset($lang)) {
            app()->setLocale($lang);
            session(['lang' => $lang]);
            $new_lang = app()->getLocale();
            //   dd($new_lang);
            return $new_lang;
        }
    }

    public function getCoordinates(Request $request)
    {
        $project = new Project;
        $coordinates = $project->get('coordinates');
        $all_cords = [];
        foreach ($coordinates as $cord) {
            $new_arr = [str_replace(`"`, '', $cord->coordinates)];
            array_push($all_cords, $new_arr);
        }
        return $all_cords;
    }

    public function addFav(Request $request)
    {
        if (isset($request->id)) {
            $id = $request->id;
            if (!is_null($request->cookie('favs'))) {
                $favs = json_decode($request->cookie('favs'), true);
                if (is_array($favs)) {
                    if (in_array($id, $favs)) {
                        foreach ($favs as $k => $v) {
                            if ($v === $id) {
                                unset($favs[$k]);
                                $favs = json_encode($favs);
                                Cookie::queue('favs', $favs, 84600);
                                return response()->json('removed');
                            }
                        }
                    } else {
                        $favs[] = $id;
                        $favs = json_encode($favs);
                        Cookie::queue('favs', $favs, 84600);
                        return response()->json('added');
                    }
                }
            } else {
                $favs = [$id];
                $favs = json_encode($favs);
                Cookie::queue('favs', $favs, 84600);
                return response()->json('added');
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect("/admin");
    }

    // Wishlist
    public function wishlist(Request $request)
    {
        $products = '';
        $favs = json_decode($request->cookie('favs'), true);
        if ($request->cookie('favs') !== null) {
            if (is_array($favs)) {
                $products = Product::with('project')->whereIn('id', $favs)->orderBy('top', 'desc')->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')->orderBy('id', 'desc')->paginate(30);
            } else {
                $products = Product::with('project')->where('id', $favs)->orderBy('top', 'desc')->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')->orderBy('id', 'desc')->paginate(30);
            }
        }
        $currency_rate = $request->has('currency_rate')?$request->currency_rate:'amd';
        $products_prices = $this->getProductsPrices($products,$currency_rate);
        $product_currency = $request->has('currency_rate')?strtoupper($request->currency_rate):'AMD';

        return view('pages.wishlist')->with(compact('products', 'favs' , 'products_prices','product_currency'));
    }

    // Layouts
    public function layouts(Request $request)
    {
        $products = Product::where('status', '!=', 'sold')->whereHas('project', function ($query) {
            $query->where('visibility', '=', 1);
        });
        $locations = Location::where('locale', session('lang'))->with('childs', 'parent')->get();
        $states = Place::whereHas('type', function ($query) {
            $query->where('parent_id', 2);
        })->with('children.children')->get();

        if (isset($request->products)) {
            if (isset($request->rooms)) {
                $room = implode(',', $request->rooms);
                $rooms = [];
                if (substr($room, -1) == '+') {
                    for ($i = (int)substr($room, 0, 1); $i <= ((int)substr($room, 0, 1) + 5); $i++) {
                        array_push($rooms, $i);
                    }
                } else {
                    $rooms = $request->rooms;
                }
                $products = $products->whereIn('post_id', $request->products)
                    ->whereIn('rooms', $rooms);
            } else {
                $products = $products->whereIn('post_id', $request->products);
            }
        } elseif (isset($request->rooms)) {
            $room = implode(',', $request->rooms);
            $rooms = [];
            if (substr($room, -1) == '+') {
                for ($i = (int)substr($room, 0, 1); $i <= ((int)substr($room, 0, 1) + 5); $i++) {
                    array_push($rooms, $i);
                }
            } else {
                $rooms = $request->rooms;
            }
            if (isset($request->products)) {
                $products = $products->whereIn('post_id', $request->products)
                    ->whereIn('rooms', $rooms);
            } else {
                $products = $products->whereIn('rooms', $rooms);
            }
        }
        $all_products_count = $products->get()->count();
        $favs = json_decode($request->cookie('favs'), true);
        if ($request->get('ajax')) {
            $offset = $request->offset;
            $all_products = $products->with('project:project_id,short_name,slug', 'currency')->orderBy('top', 'desc')->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')->offset($offset)->take(12)->get();
            $currency_rate = $request->has('currency_rate')?$request->currency_rate:'amd';
            $products_prices = $this->getProductsPrices($all_products,$currency_rate);
            $product_currency = $request->has('currency_rate')?strtoupper($request->currency_rate):'AMD';
            return response()->json(['data' => $all_products, 'total' => $all_products_count,'products_prices' => $products_prices,'product_currency' => $product_currency]);
        } else {
            $products = $products->with('project')->orderBy('top', 'desc')->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')->orderBy('post_id', 'desc')->paginate(12);
            $currency_rate = $request->has('currency_rate')?$request->currency_rate:'amd';
            $products_prices = $this->getProductsPrices($products,$currency_rate);
            $product_currency = $request->has('currency_rate')?strtoupper($request->currency_rate):'AMD';
            return view('pages.layouts')->with(compact(['products', 'favs', 'all_products_count', 'states','product_currency', 'locations','products_prices']));
        }
    }


    public function getProductsPrices($products,$currency_rate)
    {
        $currencies_cb = CurrenciesCb::first();
        $products_prices = [];
        if (gettype($products) != 'string') {
            foreach ($products as $product) {
                $project = Project::where('project_id',$product->post_id)->first();
                $currency_code = $product->currency->code;
                $product_price = $product->price;
                if($currency_code == "USD"){
                    $product_price = $product->price * $project->currency_usd;
                    if($project->get_rate_type == 'rate_from_kb'){
                        $product_price = $product->price * $currencies_cb->usd;
                    }
                }
                if($currency_code == "EUR"){
                    $product_price = $product->price * $project->currency_eur;
                    if($project->get_rate_type == 'rate_from_kb'){
                        $product_price = $product->price * $currencies_cb->eur;
                    }
                }
                if($currency_code == "RUB"){
                    $product_price = $product->price * $project->currency_rub;
                    if($project->get_rate_type == 'rate_from_kb'){
                        $product_price = $product->price * $currencies_cb->rub;
                    }
                }
                if($currency_rate!='amd'){
                    if($currency_rate == 'usd'){
                        if($project->get_rate_type == 'rate_from_kb'){
                            $product_price = $product_price / $currencies_cb->usd;
                        }else{
                            $product_price = $product_price / $project->currency_usd;
                        }
                    }
                    if($currency_rate == 'rub'){
                        if($project->get_rate_type == 'rate_from_kb'){
                            $product_price = $product_price / $currencies_cb->rub;
                        }else{
                            $product_price = $product_price / $project->currency_rub;
                        }
                    }
                    if($currency_rate == 'eur'){
                        if($project->get_rate_type == 'rate_from_kb'){
                            $product_price = $product_price / $currencies_cb->eur;
                        }else{
                            $product_price = $product_price / $project->currency_eur;
                        }
                    }
                }
                array_push($products_prices,round($product_price));
            }
        }
        return $products_prices;
    }

    
    // Layouts
    public function properties($request_id, Request $request)
    {

        $required_property = RequiredProject::find($request_id);
        if ($required_property) {
            $products = $this->getPotentialLayouts($required_property)->with('project')->orderBy('top', 'desc')->orderBy(DB::raw('ISNULL(discount), discount'), 'ASC')->orderBy('post_id', 'desc')->paginate(12)->onEachSide(2);
            $favs = json_decode($request->cookie('favs'), true);
            return view('pages.potlayouts')->with(compact(['products', 'favs', 'required_property']));
        }
        abort(404);
    }

    public function sendPotentialLayoutsToAll()
    {
        $required_properties = RequiredProperty::where('by_sms', true)->orWhere('by_email', true)->get();
        foreach ($required_properties as $required_property) {

            $last_product = $this->getPotentialLayouts($required_property)->orderBy('id', 'desc')->first();

            if ($last_product) {
                $last_product_id = $last_product->id;
                if ($required_property->last_sent_id != $last_product_id) {

                    self::sendPotentialLayouts($required_property);

                    $required_property->last_sent_id = $last_product_id;
                    $required_property->save();
                }
            }
        }
    }

    public static function sendPotentialLayouts($required_property)
    {
        $message = 'Ձեր հարցման արդյունքում գտնվել է ևս մի քանի գույք կառուցապատողից: ' . 'https://www.redgroup.am/properties/' . $required_property->id;
        $user = User::find($required_property->user_id);
        if ($required_property->by_sms) {
            $nikita = new Nikita;
            $response = $nikita->send($required_property->phone, $message);
        }
        if ($required_property->by_email) {
            Mail::raw($message, function ($message) use ($user) {
                $message->to($user->email)->subject('RED INVEST GROUP');
            });
        }

    }

    public function getPotentialLayouts($required_property)
    {
        $products = Product::where('status', '!=', 'sold')->where('id', '>', $required_property->last_sent_id)->whereHas('project', function ($query) {
            $query->where('visibility', '=', 1);
        });
        if (isset($required_property->property_type_id)) {
            $products = $products->where('property_type_id', $required_property->property_type_id);
        }
        if (isset($required_property->communities) && $required_property->communities != 0) {
            $products = $products->whereHas('project', function ($query) use ($required_property) {
                $query->where('location_id', $required_property->communities);
            });
        } elseif (isset($required_property->states)) {
            $products = $products->whereHas('project', function ($query) use ($required_property) {
                $query->whereHas('location', function ($query) use ($required_property) {
                    $query->where('parent_id', $required_property->states);
                });
            });
        }
        if (isset($required_property->rooms) && $required_property->rooms != 0) {
            $products = $products->where('rooms', $required_property->rooms);
        }
        if (isset($required_property->price_min)) {
            $products = $products->where('price', '>=', $required_property->price_min);
        }
        if (isset($required_property->price_max) && $required_property->price_max != 0) {
            $products = $products->where('price', '<=', $required_property->price_max);
        }
        return $products;
    }
}
