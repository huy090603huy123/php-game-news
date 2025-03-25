@extends('main_layouts.master')

@section('title', $post->title. ' - TDQ ')

@section('custom_css')
    <style>
        #floating-toc .toc-content { /* Thêm class toc-content để phân biệt với nút */
        transition: max-height 0.3s ease-in-out; /* Thêm transition cho hiệu ứng mượt mà */
        overflow: hidden; /* Ẩn nội dung khi max-height là 0 */
        }

        #floating-toc.hidden .toc-content {
        max-height: 0; /* Ẩn nội dung bằng cách đặt max-height là 0 */
        }

        
        .post--body.post--content{
            color: black;
            font-family: "Source Sans Pro", sans-serif;
            font-size: 18px;
        }

        .text.capitalize{
            text-transform: capitalize!important;
        }

        .author-info,
        .post-time{
            margin: 0;
            font-size: 14px!important;
            text-align: right;
        }

        /* Styles for the floating table of contents */
        #floating-toc {
            display: none; 
            position: fixed; 
            top: 100px; /* Adjust as needed */
            right: 20px; /* Adjust as needed */
            background-color: white;
            padding: 15px;
            border: 1px solid #ccc;
            z-index: 1000; 
            transition: opacity 0.3s ease-in-out; /* Add transition for smooth appearance */
        }

        #floating-toc.hidden {
            opacity: 0;
        }
    </style>
@endsection

@section('content')
    <div class="global-message info d-none"></div>

    <div class="main--breadcrumb">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="{{ route('home') }}" class="btn-link"><i class="fa fm fa-home"></i>Trang Chủ</a></li>
                <li><a href="{{ route('categories.show', $post->category ) }}" class="btn-link">{{ $post->category->name }}</a></li>
                <li class="active"><span>{{ $post->title }}</span></li>
            </ul>
        </div>
    </div>

    <div class="main-content--section pbottom--30">
        <div class="container">
            <div class="row">
                <div class="main--content col-md-8" data-sticky-content="true">
                    <div class="sticky-content-inner">
                        <div class="post--item post--single post--title-largest pd--30-0">
                            <div class="post--cats">
                                <ul class="nav">
                                    <li><span><i class="fa fa-folder-open-o"></i></span></li>
                                    @for($i = 0; $i < count($post->tags); $i++)
                                        <li><a class="text capitalize" href="{{ route('tags.show', $post->tags[$i]) }}">{{ $post->tags[$i]->name }}</a></li>
                                    @endfor
                                </ul>
                            </div>

                            <div class="post--info">
                                <ul class="nav meta">
                                    <li class="text capitalize"><a href="#">{{ $post->created_at->locale('vi')->translatedFormat('l'), }} {{ $post->created_at->locale('vi')->format('d/m/Y') }}<a></li>
                                    <li><a href="#">{{ $post->author->name }}</a></li>
                                    <li><span><i class="fa fm fa-eye"></i>{{ $post->views }}</span></li>
                                    <li><a href="#"><i class="fa fm fa-comments-o"></i>{{ count($post->comments) }}</a></li>
                                </ul>

                                <div class="title">
                                    <h2 class="post_title h4">{{ $post->title }}</h2>
                                </div>
                            </div>

                            <div id="post-content" class="post--body post--content"> {!! $post->body!!} </div>
                        </div>
                        <div class="ad--space pd--20-0-40">
                            <p class="author-info">Người viết: {{ $post->author->name }}</p>
                            <p class="post-time">Thời gian: {{ $post->created_at->locale('vi')->diffForHumans() }}</p>
                        </div>
                        <div class="post--tags">
                            <ul class="nav">
                                <li><span><i class="fa fa-tags"></i> Từ khóa </span></li>
                                @for($i = 0; $i < count($post->tags); $i++)
                                    <li><a class="text capitalize" href="{{ route('tags.show', $post->tags[$i]) }}">{{ $post->tags[$i]->name }}</a></li>
                                @endfor
                            </ul>
                        </div>
                        <div class="post--social pbottom--30">
                            <span class="title"><i class="fa fa-share-alt"></i> Chia sẻ </span>

                            <div class="social--widget style--4">
                                <ul class="nav">
                                    <li><a href="javascript:"><i class="fa fa-facebook"></i></a></li>
                                    <li><a href="javascript:"><i class="fa fa-twitter"></i></a></li>
                                    <li><a href="javascript:"><i class="fa fa-google-plus"></i></a></li>
                                    <li><a href="javascript:"><i class="fa fa-linkedin"></i></a></li>
                                    <li><a href="javascript:"><i class="fa fa-rss"></i></a></li>
                                    <li><a href="javascript:"><i class="fa fa-youtube-play"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="comment--list pd--30-0">
                            <div class="post--items-title">
                                <h2 class="h4"><span class="post_count_comment h4" >{{ count($post->comments) }} </span> bình luận</h2>
                                <i class="icon fa fa-comments-o"></i>
                            </div>
                            <ul class="comment--items nav">
                                @foreach($post->comments as $comment)
                                    <li>
                                        <div class="comment--item clearfix">
                                            <div class="comment--img float--left">
                                                <img style="border-radius: 50%; margin: auto; background-size: cover; width: 68px; height: 68px; background-image: url({{ $comment->user->image? asset('storage/'. $comment->user->image->path): asset('storage/placeholders/user_placeholder.jpg') }})" alt="">
                                            </div>
                                            <div class="comment--info">
                                                <div class="comment--header clearfix">
                                                    <p class="name">{{ $comment->user->name }}</p>
                                                    <p class="date">{{ $comment->created_at->locale('vi')->diffForHumans() }}</p>
                                                    <a href="javascript:;" class="reply"><i class="fa fa-flag"></i></a>
                                                </div>
                                                <div class="comment--content">
                                                    <p>{{ $comment->the_comment }}</p>
                                                    <p class="star">
                                                        <span class="text-left"><a href="#" class="reply"><i class="icon-reply"></i></a></span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="comment--form pd--30-0">
                            <div class="post--items-title">
                                <h2 class="h4">Viết bình luận</h2>
                                <i class="icon fa fa-pencil-square-o"></i>
                            </div>
                            <div class="comment-respond">
                                <x-blog.message:status="'success'"/>
                                @auth  
                                    <form onsubmit="return false;" autocomplete="off" method="POST" >
                                        @csrf

                                        <div class="row form-group">
                                            <div class="col-md-12">
                                                <textarea name="the_comment" id="message" cols="30" rows="5" class="form-control" placeholder="Đánh giá bài viết này"></textarea>
                                            </div>
                                        </div>
                                        <small style="color: red; font-size: 14px;" class="comment_error"> </small>
                                        <div class="form-group">
                                            <input id="input_comment" type="submit" value="Bình luận" class="send-comment-btn btn btn-primary">
                                        </div>
                                    </form>
                                @endauth

                                @guest
                                    <p class="h4">
                                        <a href="{{ route('login') }}">Đăng nhập</a> hoặc 
                                        <a href="{{ route('register') }}">Đăng ký</a> để bình luận bài viết
                                    </p>
                                @endguest
                            </div>

                        </div>
                        <div class="post--related ptop--30">
                            <div class="post--items-title" data-ajax="tab">
                                <h2 class="h4">Có thể bạn cũng thích</h2>
                            </div>
                            <div class="post--items post--items-2" data-ajax-content="outer">
                                <ul class="nav row" data-ajax-content="inner">
                                    @foreach($postTheSame as $postTheSame)
                                        <li class="col-sm-12 pbottom--30">
                                            <div class="post--item post--layout-3">
                                                <div class="post--img">
                                                    <a href="{{ route('posts.show', $postTheSame) }}" class="thumb">
                                                        <img src="{{ asset($postTheSame->image? 'storage/'.$postTheSame->image->path: 'storage/placeholders/placeholder-image.png')}}" alt="">
                                                    </a>

                                                    <div class="post--info">
                                                        <div class="title">
                                                            <h3 class="h4">
                                                                <a href="{{ route('posts.show', $postTheSame) }}" class="btn-link">{{ $postTheSame->title }}</a>
                                                            </h3>
                                                            <p style="font-size:16px" >
                                                                <span >{{ $postTheSame->excerpt }}</span>
                                                            </p>
                                                        </div>

                                                        <ul style="padding-top:10px" class="nav meta ">
                                                            <li><a href="javascript:;">{{ $postTheSame->author->name }}</a></li>
                                                            <li><a href="javascript:;">{{ $postTheSame->created_at->locale('vi')->diffForHumans() }}</a></li>
                                                            <li><a href="javascript:;"><i class="fa fm fa-comments"></i>{{ count($postTheSame->comments) }}</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="preloader bg--color-0--b" data-preloader="1">
                                    <div class="preloader--inner"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main--sidebar col-md-4 ptop--30 pbottom--30" data-sticky-content="true">
                    <div class="sticky-content-inner">
                        <x-blog.side-outstanding_posts :outstanding_posts="$outstanding_posts"/>
                        <x-blog.side-vote />
                        <x-blog.side-ad_banner />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-table-of-contents /> 
@endsection