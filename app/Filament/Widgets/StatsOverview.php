<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\B2bInquiries\B2bInquiryResource;
use App\Filament\Resources\Blogs\BlogResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\Feedbacks\FeedbackResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\B2bInquiry;
use App\Models\Blog;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Feedback;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getHeading(): ?string
    {
        return __('admin_trans.dashboard_overview');
    }

    protected function getStats(): array
    {
        $unreadMessages = ContactMessage::query()->where('is_read', false)->count();
        $unreadFeedback = Feedback::query()->where('is_read', false)->count();
        $unreadB2b = B2bInquiry::query()->where('is_read', false)->count();

        return [
            Stat::make(__('admin_trans.services_nav'), Service::query()->count())
                ->description(__('admin_trans.dashboard_total_records'))
                ->descriptionIcon(Heroicon::OutlinedWrenchScrewdriver)
                ->color('primary')
                ->url(ServiceResource::getUrl('index')),
            Stat::make(__('admin_trans.projects_nav'), Project::query()->count())
                ->description(__('admin_trans.dashboard_total_records'))
                ->descriptionIcon(Heroicon::OutlinedBriefcase)
                ->color('success')
                ->url(ProjectResource::getUrl('index')),
            Stat::make(__('admin_trans.products'), Product::query()->count())
                ->description(__('admin_trans.dashboard_total_records'))
                ->descriptionIcon(Heroicon::OutlinedCube)
                ->color('warning')
                ->url(CategoryResource::getUrl('index')),
            Stat::make(__('admin_trans.blogs'), Blog::query()->count())
                ->description(__('admin_trans.dashboard_total_records'))
                ->descriptionIcon(Heroicon::OutlinedNewspaper)
                ->color('info')
                ->url(BlogResource::getUrl('index')),
            Stat::make(__('admin_trans.contact_messages'), $unreadMessages)
                ->description(__('admin_trans.dashboard_unread'))
                ->descriptionIcon(Heroicon::OutlinedEnvelope)
                ->color($unreadMessages > 0 ? 'danger' : 'gray')
                ->url(ContactMessageResource::getUrl('index')),
            Stat::make(__('admin_trans.feedbacks'), $unreadFeedback)
                ->description(__('admin_trans.dashboard_unread'))
                ->descriptionIcon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color($unreadFeedback > 0 ? 'danger' : 'gray')
                ->url(FeedbackResource::getUrl('index')),
            Stat::make(__('admin_trans.b2b_inquiries'), $unreadB2b)
                ->description(__('admin_trans.dashboard_unread'))
                ->descriptionIcon(Heroicon::OutlinedBuildingOffice2)
                ->color($unreadB2b > 0 ? 'danger' : 'gray')
                ->url(B2bInquiryResource::getUrl('index')),
            Stat::make(__('admin_trans.categories_management'), Category::query()->count())
                ->description(__('admin_trans.dashboard_total_records'))
                ->descriptionIcon(Heroicon::OutlinedTag)
                ->color('gray')
                ->url(CategoryResource::getUrl('index')),
        ];
    }
}
