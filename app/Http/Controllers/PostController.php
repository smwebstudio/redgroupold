<?php
    
    namespace App\Http\Controllers;
    
    use App\Post;
    use Illuminate\Http\Request;
    use App\User;
    use App\Taxonomy;
    
    class PostController extends Controller
    {

        /**
         * Display a listing of the resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function index($lang, Request $request)
        {
            if (isset($request->taxonomy)) {
                $taxonomy = Taxonomy::find($request->taxonomy);
                $posts = $taxonomy->posts()->orderBy('id', 'DESC')->limit(3)->get();
            } else {
                $posts = Post::where(['locale' => $lang, 'post_type_id' => 1])->orderBy('id', 'DESC')->limit(3)->get();
            }
            $categories = Taxonomy::where(['taxonomy_type_id' => 2, 'locale' => app()->getLocale()])->get();
            $top_posts = Post::where(['post_type_id' => 1, 'locale' => app()->getLocale()])->orderBy('view', 'ASC')->limit(3)->get();
            
            return view('pages.posts.index')->with([
                'posts' => $posts,
                'categories' => $categories,
                'top_posts' => $top_posts
            ]);
            
        }
        
        public function loadPosts($lang, Request $request)
        {
            $request->validate([
                'id' => 'integer',
                'taxonomy_id' => 'integer|nullable'
            ]);
            if ($request->ajax()) {
                $limit = 3;
                $show_btn = false;
                if (isset($request->taxonomy_id)) {
                    $taxonomy = Taxonomy::find($request->taxonomy_id);
                    $data = $taxonomy->posts()->where('post_id', '<', $request->id)->orderBy('id', 'DESC')->limit($limit)->get();
                } else {
                    $data = Post::where([['id', '<', $request->id], ['locale', $lang]])->orderBy('id', 'DESC')->limit($limit)->get();
                }
                $output = '';
                $last_id = '';
                
                if (!$data->isEmpty()) {
                    foreach ($data as $post) {
                        $route = route('post.show', ['locale' => app()->getLocale(), 'post' => $post]);
                        $output .= '
                                  <div class="post-item">
                                        <a href="' . $route . '">
                                            <h3 class="post-title">' . $post->title . '</h3>
                                            <div class="created_date"><i class="far fa-calendar">
                                                </i><span>' . $post->created_at . '</span>
                                            </div>
                                            <div class="thumbnail"><img src ="' . $post->thumbnail . '" alt="No image"></div>
                                            <p class="excerpt">' . $post->title . '</p>
                                            <span class="read-more">' . trans('common.read_more') . '</span>
                                        </a>
                                    </div><!-- .post-item -->';
                        $last_id = $post->id;
                    }
                    
                }
                if ($data->count() == $limit) {
                    $show_btn = true;
                }
                return response()->json(['output' => $output, 'show_btn' => $show_btn, 'last_id' => $last_id]);
            }
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
         * Display the specified resource.
         *
         * @param \App\Post $post
         * @return \Illuminate\Http\Response
         */
        public function show($lang, $slug)
        {
            $post = Post::where(['locale' => $lang, 'slug' => $slug])->first();
            //dd($post);
            //$post->view += 1;
            //$post->save();
            if ($post->post_type_id == 1) {
                $top_posts = Post::where(['post_type_id' => 1, 'locale' => app()->getLocale()])->orderBy('view', 'ASC')->limit(3)->get();
                $tags = $post->taxonomies()->where('taxonomy_type_id', '=', 1)->get();
                $categories = $post->taxonomies()->where(['taxonomy_type_id' => 2, 'locale' => app()->getLocale()])->get();
                $post_all_lang = Post::where('parent_id', $post->parent_id)->get();
                $slugs = array();
                foreach ($post_all_lang as $post_lang) {
                    $slugs[$post_lang->locale] = $post_lang->slug;
                }
                return view('pages.posts.show')->with([
                    'post' => $post,
                    'slugs' => $slugs,
                    'tags' => $tags,
                    'categories' => $categories,
                    'all_cats' => Taxonomy::where(['taxonomy_type_id' => 2, 'locale' => app()->getLocale()])->get(),
                    'top_posts' => $top_posts
                ]);
            } else {
                return view('pages.posts.page')->with(['post' => $post]);
            }
        }
        
        /**
         * Show the form for editing the specified resource.
         *
         * @param \App\Post $post
         * @return \Illuminate\Http\Response
         */
        public function edit(Post $post)
        {
            //
        }
        
        /**
         * Update the specified resource in storage.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Post $post
         * @return \Illuminate\Http\Response
         */
        public function update(Request $request, Post $post)
        {
            //
        }
        
        /**
         * Remove the specified resource from storage.
         *
         * @param \App\Post $post
         * @return \Illuminate\Http\Response
         */
        public function destroy(Post $post)
        {
            //
        }
    }
