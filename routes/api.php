<?php

use Carbon\Carbon;
use App\Models\Page;
use App\Models\Banner;
use App\Models\Post;
use App\Models\Contacts;
use App\Models\User;
use App\Models\Staff;
use App\Models\Gallery;
use App\Models\SchoolType;
use App\Models\Testimonial;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Standard;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\StaffResource;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\EventsResource;
use App\Http\Resources\StandardsResource;
use App\Http\Resources\PostCategoryResource;
use Illuminate\Contracts\Database\Eloquent\Builder;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Health Check (MANDATORY in k8s)
Route::get('/health', fn () => response()->json(['status' => 'ok']));


Route::get('v1/users/{id}', function ($id) {
    return User::findOrFail($id);
});

Route::get('v1/about/{slug}', function ($slug) {
    return Page::all()
        ->where('slug', $slug)
        ->first();
})->name('about');


Route::get('v1/academics/{slug}', function ($slug) {
    return PostCategoryResource::collection(PostCategory::where('slug', $slug)->get());
})->name('academics');

Route::get('v1/staffs', function () {
    $staffs = StaffResource::collection(Staff::all());
    return $staffs->groupBy(['staff_type_id', 'school_type_id']);
})->name('staffs');

Route::get('v1/achievements', function () {
    return PostCategoryResource::collection(PostCategory::where('slug', 'achievements')->get());
})->name('academics');

Route::get('v1/achievements/{id}', function ($id) {
    $postCategory = PostCategory::where('slug', 'achievements')->first();

    if (!$postCategory) {
        return response()->json(['message' => 'Achievements category not found'], 404);
    }

    $post = $postCategory->posts()->where('id', $id)->with('school_type')->first();

    if (!$post) {
        return response()->json(['message' => 'Achievement post not found'], 404);
    }

    return response()->json($post);
});

Route::get('v1/activities/{school_type}', function ($school_type) {

    $school = SchoolType::firstWhere('slug', $school_type);
    if (!$school) {
        return response()->json(['message' => 'School type not found'], 404);
    }
    $schoolId = $school->id;
    $postCategory = PostCategory::firstWhere('slug', 'activities');
    
    if (!$postCategory) {
        return response()->json(['school' => $school->name, 'posts' => []]);
    }
    
    $posts = $postCategory->posts()->where('school_type_id', $schoolId)->orderBy('created_at', 'desc')->get();
    return [
        'school' => $school->name,
        'posts' => $posts,
    ];
})->name('academics');

Route::get('v1/activities', function () {
    return Post::all();
});

Route::get('v1/testimonials', function () {
    return Testimonial::all();
})->name('testimonials');

Route::get('v1/galleries', function (Request $request) {
    $query = Gallery::with('school_type');

    if ($request->has('media_type')) {
        $query->where('media_type', $request->media_type);
    }

    $galleries = $query->get();

    return response()->json([
        'data' => $galleries,
    ]);
});

Route::get('v1/galleries/{school_type}', function ($school_type) {
    $school = SchoolType::where('slug', $school_type)->first();
    if (!$school) {
        return response()->json([
            'message' => 'School type not found.',
        ], 404);
    }

    $galleries = Gallery::where('school_type_id', $school->id)
        ->with('school_type')
        ->get();

    return response()->json([
        'school' => $school->name,
        'data' => $galleries,
    ]);
});

Route::get('v1/school-types', function () {
    return SchoolType::all();
});

Route::get('v1/announcements', function () {
    $currentDate = Carbon::now()->setTimezone('Asia/Kolkata')->toDateTimeString();

    return AnnouncementResource::collection(
        Announcement::with('school_type', 'user')
            ->where('start_date', '<=', $currentDate)  
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('end_date')           
                      ->orWhere('end_date', '>=', $currentDate); 
            })
            ->orderBy('published_at', 'desc')
            ->get()
    );
});

Route::get('v1/events', function () {
    return EventsResource::collection(
        Event::with('school_type', 'user')
            ->orderBy('published_at', 'desc')
            ->get()
    );
});

Route::get('v1/events/recent', function () {
    $currentDate = Carbon::now()->setTimezone('Asia/Kolkata')->toDateTimeString();

    return EventsResource::collection(
        Event::with('school_type', 'user')
            ->where('start_date', '<=', $currentDate)  
            ->where(function ($query) use ($currentDate) {
                $query->whereNull('end_date')           
                      ->orWhere('end_date', '>=', $currentDate); 
            })
            ->orderBy('published_at', 'desc')
            ->get()
    );
});

Route::get('v1/events/{id}', function ($id) {
    $event = Event::with('school_type', 'user')->find($id);

    if (!$event) {
        return response()->json(['message' => 'Event not found'], 404);
    }

    return new EventsResource($event);
});

Route::get('v1/standards', function () {
    return StandardsResource::collection(
        Standard::with('school_type', 'user')
            ->get()
    );
});

Route::get('v1/banners', function () {
    return response()->json([
        'data' => Banner::all(),
    ]);
});

Route::post('v1/contacts', function(Request $request){
    $validator = Validator::make($request->all(),[
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);
    if($validator-> fails()){
        return response()->json(['message'=>'Validation Failed', 'errors'=> $validator->errors()],422);
    }
    Contacts::create($request->only('name', 'email', 'subject', 'message'));

    return response()-> json(['message'=> 'Thank you for Contacting Us!'], 200);
});

