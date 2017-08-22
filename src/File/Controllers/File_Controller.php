<?php

namespace CPM\File\Controllers;

use WP_REST_Request;
use CPM\File\Models\File;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use CPM\Transformer_Manager;
use CPM\File\Transformer\File_Transformer;

class Task_Controller {
    use Transformer_Manager;

    public function index( WP_REST_Request $request ) {
        $tasks = File::paginate();

        $task_collection = $tasks->getCollection();

        $resource = new Collection( $task_collection, new File_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $tasks ) );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $task_id    = $request->get_param( 'task_id' );

        $task = File::where( 'id', $task_id )
            ->where( 'project_id', $project_id )
            ->first();

        $resource = new Item( $task, new Task_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        
        $data = $request->get_params();
        $data = array_filter( $data );
        $task = File::create( $data );

        $resource = new Item( $task, new File_Transformer );

        return $this->get_response( $resource );
    }

    public function update( WP_REST_Request $request ) {
        $data = $request->get_params();

        $task = File::find( $data['task_id'] );
                
        $task->update( array_filter( $data ) );
        
        $resource = new Item( $task, new File_Transformer );

        return $this->get_response( $resource );
    }

    public function destroy( WP_REST_Request $request ) {
        $project_id = $request->get_param( 'project_id' );
        $task_id    = $request->get_param( 'task_id' );

        $task = File::where( 'id', $task_id )
            ->where( 'project_id', $project_id )
            ->first();

        $task->delete();
    }
}