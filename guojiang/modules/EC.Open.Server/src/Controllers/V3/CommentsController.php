<?php

namespace GuoJiangClub\EC\Open\Server\Controllers\V3;

use GuoJiangClub\Component\Order\Models\Comment;
use GuoJiangClub\EC\Open\Server\Transformers\CommentTransformer;

class CommentsController extends Controller
{
	public function index()
	{
		$user = request()->user();

		$comments = Comment::where('user_id', $user->id)
			->with('goods')->with('orderItem')->with('user')->orderBy('created_at', 'desc')->paginate(10);

		return $this->response()->paginator($comments, new CommentTransformer());
	}
}