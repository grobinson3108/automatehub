export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    subscription_type: 'free' | 'premium' | 'pro';
    is_professional: boolean;
    company?: string;
    phone?: string;
    n8n_level: 'beginner' | 'intermediate' | 'expert';
    is_admin: boolean;
    created_at: string;
    updated_at: string;
}

export interface Tutorial {
    id: number;
    title: string;
    description: string;
    content: string;
    video_url?: string;
    difficulty_level: 'beginner' | 'intermediate' | 'expert';
    duration_minutes: number;
    is_premium: boolean;
    is_pro: boolean;
    category: Category;
    tags: Tag[];
    downloads_count: number;
    views_count: number;
    created_at: string;
    updated_at: string;
}

export interface Category {
    id: number;
    name: string;
    slug: string;
    description?: string;
    icon?: string;
    color?: string;
    is_featured: boolean;
    tutorials_count: number;
    created_at: string;
    updated_at: string;
}

export interface Tag {
    id: number;
    name: string;
    slug: string;
    color?: string;
    created_at: string;
    updated_at: string;
}

export interface Badge {
    id: number;
    name: string;
    description: string;
    type: 'completion' | 'streak' | 'milestone' | 'special';
    icon: string;
    color: string;
    criteria: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface UserBadge {
    id: number;
    user_id: number;
    badge_id: number;
    badge: Badge;
    earned_at: string;
    is_featured: boolean;
}

export interface UserTutorialProgress {
    id: number;
    user_id: number;
    tutorial_id: number;
    tutorial: Tutorial;
    completed_at?: string;
    progress_percentage: number;
    last_accessed_at: string;
    is_favorited: boolean;
}

export interface BlogPost {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    content: string;
    featured_image?: string;
    is_published: boolean;
    published_at?: string;
    meta_title?: string;
    meta_description?: string;
    reading_time: number;
    views_count: number;
    created_at: string;
    updated_at: string;
}

export interface Analytics {
    id: number;
    user_id?: number;
    event_type: string;
    event_data: Record<string, any>;
    ip_address: string;
    user_agent: string;
    created_at: string;
}

export interface PageProps {
    auth: {
        user: User | null;
    };
    name: string;
    quote: {
        message: string;
        author: string;
    };
    flash: {
        success?: string;
        error?: string;
        warning?: string;
        info?: string;
    };
    csrf_token: string;
    ziggy: any;
}


export interface PaginatedData<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export interface HomePageProps extends PageProps {
    featuredTutorials: Tutorial[];
    categories: Category[];
    stats: {
        totalTutorials: number;
        totalUsers: number;
        totalDownloads: number;
        avgRating: number;
    };
}

export interface TutorialsPageProps extends PageProps {
    tutorials: PaginatedData<Tutorial>;
    categories: Category[];
    filters: {
        category?: string;
        difficulty?: string;
        search?: string;
    };
}

export interface DashboardPageProps extends PageProps {
    recentTutorials: Tutorial[];
    progress: UserTutorialProgress[];
    badges: UserBadge[];
    stats: {
        completedTutorials: number;
        totalBadges: number;
        streakDays: number;
        totalWatchTime: number;
    };
}

export interface TutorialPageProps extends PageProps {
    tutorial: Tutorial;
    relatedTutorials: Tutorial[];
    userProgress?: UserTutorialProgress;
    canAccess: boolean;
    downloadUrl?: string;
}