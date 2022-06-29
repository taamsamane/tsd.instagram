<?php

namespace TSD\Instagram\Services;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class BasicDisplay extends Controller
{

	public $redirectUrl = 'https://taam.insta.test/auth/';

	public $clientID;

	public $clientSecret;

	public $access_token;

	public $posts = [];

	public function __construct()
	{
		$this->clientID  = config('instagram.client_id');
		$this->clientSecret  = config('instagram.client_secret');
	}

	public function setRedirectURL($url)
	{
		$this->redirectUrl = $url;
		return $this;
	}

	public function getPosts()
	{
		if (!$this->access_token)
			throw new Exception("First Get Access Token");

		$req = Http::get('https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url&access_token=' . $this->access_token);
		
		if (!$req->successful())
			throw new Exception($req->body());

		$res = $req->json();
		$this->posts = array_merge($res['data'], $this->posts);
		if (isset($res['paging']['next']))
			$this->getNextPages($res['paging']['next']);

		return $this->posts;
	}

	public function getAccessToken()
	{
		$params = [
			'client_id' => $this->clientID,
			'client_secret' => $this->clientSecret,
			'grant_type' => 'authorization_code',
			'code' => request()->code,
			'redirect_uri' => $this->redirectUrl
		];

		$req = Http::asForm()->post('https://api.instagram.com/oauth/access_token', $params);


		if (!$req->successful())
			throw new Exception($req->body());

		$res = $req->json();
		$this->access_token = $res['access_token'];
		return $this;
	}

	public function requestUserAccess()
	{

		return Redirect::to("https://www.instagram.com/oauth/authorize?client_id=$this->clientID&redirect_uri=$this->redirectUrl&scope=user_profile,user_media&response_type=code");
	}

	private function getNextPages($url)
	{
		$req = Http::get($url);
		
		if (!$req->successful())
			throw new Exception($req->body());

		$res = $req->json();
		if ($req->successful()) {
			$this->posts = array_merge($res['data'], $this->posts);
			if (isset($res['paging']['next']))
				$this->getNextPages($res['paging']['next']);
		}
	}
}
