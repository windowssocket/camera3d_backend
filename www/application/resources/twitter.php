<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Twitter extends CI_Controller
{	
	private $connection;	
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('twitteroauth');
		
		$this->config->load('twitter');
		
		if($this->session->userdata('access_token') && $this->session->userdata('access_token_secret'))
		{
			
			$this->connection = $this->twitteroauth->create($this->config->item('twitter_consumer_token'), $this->config->item('twitter_consumer_secret'), $this->session->userdata('access_token'),  $this->session->userdata('access_token_secret'));
                       
		}
		elseif($this->session->userdata('request_token') && $this->session->userdata('request_token_secret'))
		{
			
			$this->connection = $this->twitteroauth->create($this->config->item('twitter_consumer_token'), $this->config->item('twitter_consumer_secret'), $this->session->userdata('request_token'), $this->session->userdata('request_token_secret'));
		
                        
                }
		else
		{
                    
			$this->connection = $this->twitteroauth->create($this->config->item('twitter_consumer_token'), $this->config->item('twitter_consumer_secret'));
		}
	}
		
	public function auth($token, $secret_token){
		if($this->session->userdata('request_token') && $this->session->userdata('request_token_secret'))
		{
                    echo "already login";
                    $content = $this->connection->get('account/verify_credentials');
                    var_dump($content);   
			// User is already authenticated. Add your user notification code here.			
		}
		else
		{
			// Making a request for request_token
			//$request_token = $this->connection->getRequestToken(base_url('/twitter/callback'));

			$this->session->set_userdata('request_token', $token);
			$this->session->set_userdata('request_token_secret', $secret_token);
			
			if($this->connection->http_code == 200)
			{
                                $content = $this->connection->get('account/verify_credentials');               
			}
			else
			{
                            echo "not login";
                            $content = $this->connection->get('account/verify_credentials');
                            var_dump($content);
				// An error occured. Make sure to put your error notification code here.
				//redirect(base_url('/'));
			}
		}
	}
	
	
	public function callback()
	{
		if($this->input->get('oauth_token') && $this->session->userdata('request_token') !== $this->input->get('oauth_token'))
		{
			$this->reset_session();
			redirect(base_url('/twitter/auth'));
		}
		else
		{
			$access_token = $this->connection->getAccessToken($this->input->get('oauth_verifier'));
                        
			if ($this->connection->http_code == 200)
			{
				$this->session->set_userdata('access_token', $access_token['oauth_token']);
				$this->session->set_userdata('access_token_secret', $access_token['oauth_token_secret']);
				$this->session->set_userdata('twitter_user_id', $access_token['user_id']);
				$this->session->set_userdata('twitter_screen_name', $access_token['screen_name']);

				$this->session->unset_userdata('request_token');
				$this->session->unset_userdata('request_token_secret');
				
				redirect(base_url('/'));
			}
			else
			{
				// An error occured. Add your notification code here.
				redirect(base_url('/'));
			}
		}
	}
	
	public function post($in_reply_to)
	{
		$message = $this->input->post('message');
		if(!$message || mb_strlen($message) > 140 || mb_strlen($message) < 1)
		{
			// Restrictions error. Notification here.
			redirect(base_url('/'));
		}
		else
		{
			if($this->session->userdata('access_token') && $this->session->userdata('access_token_secret'))
			{
				$content = $this->connection->get('account/verify_credentials');
				if(isset($content->errors))
				{
					// Most probably, authentication problems. Begin authentication process again.
					$this->reset_session();
					redirect(base_url('/twitter/auth'));
				}
				else
				{
					$data = array(
						'status' => $message,
						'in_reply_to_status_id' => $in_reply_to
					);
					$result = $this->connection->post('statuses/update', $data);

					if(!isset($result->errors))
					{
						// Everything is OK
						redirect(base_url('/'));
					}
					else
					{
						// Error, message hasn't been published
						redirect(base_url('/'));
					}
				}
			}
			else
			{
				// User is not authenticated.
				redirect(base_url('/twitter/auth'));
			}
		}
	}
	
	
	public function reset_session()
	{
		$this->session->unset_userdata('access_token');
		$this->session->unset_userdata('access_token_secret');
		$this->session->unset_userdata('request_token');
		$this->session->unset_userdata('request_token_secret');
		$this->session->unset_userdata('twitter_user_id');
		$this->session->unset_userdata('twitter_screen_name');
	}
}