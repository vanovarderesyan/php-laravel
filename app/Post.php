<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title','content','user_id'];

    public function category()
    {
        return $this->hasOne(Category::class);
    }

    public function author()
    {
        return $this->hasOne(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'tag_id');
    }

    public function sluggable()
    {
        return [
            'slug'=> [
                'source' => 'title'
            ]
        ];
    }

    public static function add($fields)
    {
       $post = new static;
       $post->fill($fields);
       $post->user_id = 1;
       $post->save();

       return $post;
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {   
        Storage::delete('uploads/'.$this->image);
        $this->delete();
    }

    public function uploadImage($image)
    {
        if ($image == null) {
            return;
        }
        Storage::delete('uploads/'.$this->image);
        $filename = str_random(10) . '.' . $image -> extension();
        $image->saveAs('uploads',$filename);
        $this->image = $filename;
        $this->save();
    }

    public function setCategory($id)
    {
        if ($id == null) {
            return;
        }

        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids)
    {
        if ($ids == null) {
            return;
        }

        $this->tags()->sync($ids);
    }

    public function setDraft()
    {   
        $this->status = 0;
        $this->save();
    }
    
    public function setPublic()
    {   
        $this->status = 1;
        $this->save();
    }

    public function toggleStatus($value)
    {
        if ($value == null) {
            return $this->setDraft();
        }

        return $this->setPublic();
    }

    public function setFeaturend()
    {   
        $this->is_featured = 1;
        $this->save();
    }
    
    public function setStandart()
    {   
        $this->is_featured = 0;
        $this->save();
    }

    public function toggleFeatured($value)
    {
        if ($value == null) {
            return $this->setStandart();
        }

        return $this->setFeaturend();
    }
}

$post = Post::find(1);
