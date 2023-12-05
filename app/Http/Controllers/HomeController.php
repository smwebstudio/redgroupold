<?php
    
    namespace App\Http\Controllers;
    
    use App\AnnouncementOptionType;
    use App\AnnouncementType;
    use App\Currency;
    use App\Place;
    use App\PlaceType;
    use Illuminate\Http\Request;
    use App\Announcement;
    use App\User;
    use App\Comment;
    use App\Setting;
    use App\Post;
    use App\PropertyType;

    class HomeController extends Controller
    {
        private $homepage_parent_id;
        
        public function __construct()
        {
            $this->homepage_parent_id = Setting::where('name', 'home_page_parent_id')->first()->value;
        }
        
        public function index()
        {
            $locale = session('lang') ?? app()->getLocale();
            $homepages = Post::where('parent_id', $this->homepage_parent_id)->get()->keyBy('locale');
            if (!isset($homepages[$locale])) {
                $homepage = $homepages['hy'];
                $locale = $this->changeLang('hy');
            } else {
                $homepage = $homepages[$locale];
            }
            // dd($locale);
            // if ($homepage) {
                $announcement = new Announcement;
                $comments = new Comment;
             
                $top_suggestions = $announcement->getTopAnnouncementsBy('top');

                $home_top_slider = json_decode(Setting::where('name', 'home_top_slider')->first()->value, true);
                
                $urgent_suggestions = $announcement->getTopAnnouncementsBy('urgent');
                $realtors = User::where('user_type_id', 4)->take(4)->get();
                $comments = Comment::where([['post_type_id', $homepage->post_type_id], ['post_id', $homepage->parent_id]])->get();
                $hot_offer = $announcement->getHotOffer();
                $content = json_decode($homepage->content);

                $property_types = PropertyType::where('locale', $locale)->get();
                $announcement_types = AnnouncementType::where('locale', $locale)->get();

                $states = Place::whereHas('type', function($query) use ($locale){
                    $query->where([['locale', $locale], ['parent_id', 2]]);
                })->with('children.children')->get();

                $opt_types = AnnouncementOptionType::where('locale', $locale)->whereIn('parent_id', [13, 52, 109, 106, 22, 1, 4, 46, 103, 73, 94, 130, 133, 136, 97, 139, 142, 145, 148, 106, 100])->with(['options' => function($query) use ($locale){
                    $query->where('locale',$locale);
                }])->get();
                $option_types = array();
                $option_parents = array();
                foreach ($opt_types as $option_type)
                {
                    $option_types[$option_type->id] = $option_type;
                    $option_parents[$option_type->parent_id] = $option_type;
                }

                $currencies = Currency::where('locale', $locale)->get();

                $currency = Setting::where('name', 'price_options_1')->first()->value;
                $currency_ids = json_decode($currency, true);

                $currency_area = Setting::where('name', 'area_price_options')->first()->value;
                $currency_area_ids = json_decode($currency_area, true);
                
                return view('pages.home')->with([
                    'top_suggestions' => $top_suggestions,
                    'announcement_types' => $announcement_types,
                    'urgent_suggestions' => $urgent_suggestions,
                    'realtors' => $realtors,
                    // 'comments' => $comments,
                    'hot_offer' => $hot_offer,
                    'content' => $content,
                    'property_types' => $property_types,
                    'home_top_slider' => $home_top_slider,
                    'states' => $states,
                    'option_types' => $option_types,
                    'option_parents' => $option_parents,
                    'currencies' => $currencies,
                    'currency_ids' => $currency_ids,
                    'currency_area_ids' => $currency_area_ids
                ]);
            // }
            
            // return redirect(route('error', $locale));
        }
        
        public function changeLang($lang) {
			if(isset($lang)) {
				app()->setLocale($lang);
				session(['lang' => $lang]);
				$new_lang = app()->getLocale();
				return $new_lang;
			}
		}
    }
