<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'hero' => $this['hero']->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'sub_title' => $item->sub_title,
                    'text' => $item->text,
                    'button_text' => $item->button_text,
                    'button_link_url' => $item->button_link_url,
                    'image' => $item->getFirstMediaUrl('sections'),
                    'alt_image' => $item->alt_image,
                    'order' => $item->order,
                    'is_active' => $item->is_active,
                ];
            }),
            'about_us' => [
                'id' => $this['about_us']->id,
                'title' => $this['about_us']->title,
                'sub_title' => $this['about_us']->sub_title,
                'text' => $this['about_us']->text,
                'image' => $this['about_us']->getFirstMediaUrl('sections'),
                'alt_image' => $this['about_us']->alt_image,
                'order' => $this['about_us']->order,
                'is_active' => $this['about_us']->is_active,
                'button_text' => $this['about_us']->button_text,
                'button_link_url' => $this['about_us']->button_link_url,
                'statistics' => $this['statistics_section']->map(function ($item) {
                    return SectionResource::make($item);
                }),
            ],
            // 'categories_section' => [
            //     'id' => $this['categories_section']->id,
            //     'title' => $this['categories_section']->title,
            //     'sub_title' => $this['categories_section']->sub_title,
            //     'text' => $this['categories_section']->text,
            //     'image' => $this['categories_section']->getFirstMediaUrl('sections'),
            //     'alt_image' => $this['categories_section']->alt_image,
            //     'order' => $this['categories_section']->order,
            //     'is_active' => $this['categories_section']->is_active,
            //     'button_text' => $this['categories_section']->button_text,
            //     'button_link_url' => $this['categories_section']->button_link_url,
            //     'categories' => CategoryResource::collection($this['categories']),
            // ],
            'video_section' => [
                'id' => $this['video_section']->id,
                'title' => $this['video_section']->title,
                'sub_title' => $this['video_section']->sub_title,
                'text' => $this['video_section']->text,
                'image' => $this['video_section']->getFirstMediaUrl('sections'),
                'alt_image' => $this['video_section']->alt_image,
                'order' => $this['video_section']->order,
                'is_active' => $this['video_section']->is_active,
                'button_text' => $this['video_section']->button_text,
                'button_link_url' => $this['video_section']->button_link_url,
            ],
            'why_choose_us_section' => [
                'id' => $this['why_choose_us_section']->id,
                'title' => $this['why_choose_us_section']->title,
                'sub_title' => $this['why_choose_us_section']->sub_title,
                'text' => $this['why_choose_us_section']->text,
                'image' => $this['why_choose_us_section']->getFirstMediaUrl('sections'),
                'alt_image' => $this['why_choose_us_section']->alt_image,
                'order' => $this['why_choose_us_section']->order,
                'is_active' => $this['why_choose_us_section']->is_active,
                'button_text' => $this['why_choose_us_section']->button_text,
                'button_link_url' => $this['why_choose_us_section']->button_link_url,
                'benefits' => $this['benefits']->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'sub_title' => $item->sub_title,
                        'text' => $item->text,
                        'image' => $item->getFirstMediaUrl('sections'),
                        'alt_image' => $item->alt_image,
                        'order' => $item->order,
                        'is_active' => $item->is_active,
                    ];
                }),
            ],
            'blogs_section' => [
                'id' => $this['blogs_section']->id,
                'title' => $this['blogs_section']->title,
                'sub_title' => $this['blogs_section']->sub_title,
                'text' => $this['blogs_section']->text,
                'image' => $this['blogs_section']->getFirstMediaUrl('sections'),
                'alt_image' => $this['blogs_section']->alt_image,
                'order' => $this['blogs_section']->order,
                'is_active' => $this['blogs_section']->is_active,
                'button_text' => $this['blogs_section']->button_text,
                'button_link_url' => $this['blogs_section']->button_link_url,
                'blogs' => BlogResource::collection($this['blogs']),
            ],
        ];
    }
}
