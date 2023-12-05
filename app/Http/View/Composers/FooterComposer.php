<?php
    
    
    namespace App\Http\View\Composers;
    
    use Illuminate\View\View;
    use App\Announcement;
    use App\Post;

    class FooterComposer
    {
        private $announcement;
        
        private $post;
        
        public function __construct()
        {
            $this->announcement = new Announcement();
            $this->post = new Post();
        }
    
        /**
         * Bind data to the view.
         *
         * @param View $view
         * @return void
         */
        public function compose(View $view)
        {
            $top_announcements = $this->announcement->getTopAnnouncementsBy('top');
            $top_posts = $this->post->getBlogPosts(app()->getLocale());
            $view->with([
                'top_announcements' => $top_announcements,
                'top_posts' => $top_posts
            ]);
        }
    }
