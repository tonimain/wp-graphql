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
			'role'       => 'administrator'
		] );

	}

	public function tearDown() {
		parent::tearDown();
	}

	public function createCommentObject( $args = [] ) {
		$defaults = [
			'comment_author'       => $this->admin,
			'comment_author_email' => 'admin@example.org',
			'comment_author_url'   => 'www.wpgraphql.com',
			'comment_content'      => 'Test comment content',
			'comment_approved'     => 1,
		];

		$args = array_merge( $defaults, $args );

		$comment_id = $this->factory->comment->create( $args );

		$this->comment_object_return = array(
			'comment_id'           => $comment_id,
			'comment_author_email' => $args['comment_author_email'],
			'comment_author_url'   => $args['comment_author_url']
		);

		return $this->comment_object_return;
	}

	public function create_comments( int $amount ) {
		$created_comments = [];

		for ( $i = 1; $i <= $amount; $i ++ ) {
			$created_comments[ $i ] = $this->createCommentObject( [
				'comment_content' => $i
			] );
		}

		return array( $created_comments, $amount );

	}

	public function testCommentConnectionQueryArgsAuthorEmail() {
		$this->create_comments( 2 );
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

		$edge_count = 2;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

	public function testCommentConnectionQueryArgsAuthorUrl() {
		$this->create_comments( 2 );
		$author_url = $this->comment_object_return['comment_author_url'];

		$query = '
		{
		  comments(where: {authorUrl: "' . $author_url . '"}) {
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

		$edge_count = 2;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

	public function testCommentConnectionQueryArgsAuthorIn() {
		$this->create_comments( 2 );
		$author_in = $this->admin;

		$query = '
		{
		  comments(where: {authorIn: "' . $author_in . '"}) {
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

		$edge_count = 2;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

	public function testCommentConnectionQueryArgsAuthorNotIn() {
		$this->create_comments( 2 );
		$author_in = $this->admin;

		$query = '
		{
		  comments(where: {authorNotIn: "' . $author_in . '"}) {
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

		$edge_count = 2;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

	public function testCommentConnectionQueryArgsCommentIn() {
		$this->create_comments( 2 );
		$comment_id = $this->comment_object_return['comment_id'];

		$query = '
		{
		  comments(where: {commentIn: "' . $comment_id . '"}) {
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

		$edge_count = 2;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

	public function testCommentConnectionQueryArgsCommentNotIn() {
		$this->create_comments( 2 );
		$comment_id = $this->comment_object_return['comment_id'];

		$query = '
		{
		  comments(where: {commentNotIn: "' . $comment_id . '"}) {
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

		$edge_count = 2;

		foreach ( $edges as $edge ) {
			$this->assertArrayHasKey( 'node', $edge );
			$this->assertEquals( $edge['node']['content'], (string) $edge_count );

			$edge_count --;
		}
	}

	//TODO: karma, orderby, parent, parentIn, parentNotIn, contentAuthorIn
	//contentAuthorNotIn, contentId, contentIdIn, contentIdNotIn, contentAuthor
	//contentStatus, contentType, contentName, contentParent, search, status,
	//commentType, commentTypeIn, commentTypeNotIn, userId

}