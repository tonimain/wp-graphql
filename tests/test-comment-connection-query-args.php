<?php

class WP_GraphQL_Test_Comment_Connection_Query_Args extends WP_UnitTestCase {
	public $current_time;
	public $current_date;
	public $current_date_gmt;
	public $admin;
	private $comment_object_return;

	public function setUp() {
		parent::setUp();

		$this->post_id = $this->factory->post->create();


		$this->current_time     = strtotime( '- 1 day' );
		$this->current_date     = date( 'Y-m-d H:i:s', $this->current_time );
		$this->current_date_gmt = gmdate( 'Y-m-d H:i:s', $this->current_time );
		$this->admin            = $this->factory->user->create( [
			'role'       => 'administrator',
			'user_email' => 'user@test.com',
		] );

		$this->created_comment_ids = $this->create_comments();

	}

	public function tearDown() {
		parent::tearDown();
	}

	public function createCommentObject( $args = [] ) {
		$defaults = [
			'comment_author'       => $this->admin,
			'comment_author_email' => 'admin@example.org',
			'comment_content'      => 'Test comment content',
			'comment_approved'     => 1,
		];

		$args = array_merge( $defaults, $args );

		$comment_id = $this->factory->comment->create( $args );

		$this->comment_object_return = array( 'comment_id'           => $comment_id,
		                                      'comment_author_email' => $args['comment_author_email']
		);

		return $this->comment_object_return;
	}

	public function create_comments() {
		$created_comments = [];

		for ( $i = 1; $i <= 10; $i ++ ) {
			$created_comments[ $i ] = $this->createCommentObject( [
				'comment_content' => $i
			] );
		}

		return $created_comments;

	}

	public function testCommentConnectionQueryArgsAuthorEmail() {
		$author_email = $this->comment_object_return['comment_author_email'];

		$query = '
		{
		  comments(where: {authorEmail: "' . $author_email . '"}) {
		    edges {
		      node {
		        id
		        content
		      }
		    }
		  }
		}
		';

		$actual = do_graphql_request( $query );

		$edges = $actual['data']['comments']['edges'];
		$this->assertNotEmpty( $edges );

		$edge_count = 10;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

}