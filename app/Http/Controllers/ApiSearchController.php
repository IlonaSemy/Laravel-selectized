<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Product;
class ApiSearchController extends MainController {
    
    public function appendValue($data, $type, $element)
	{
		// operate on the item passed by reference, adding the element and type
		foreach ($data as $key => & $item) {
			$item[$element] = $type;
		}
		return $data;		
	}
		
	public function appendURL($data, $prefix)
	{
		// operate on the item passed by reference, adding the url based on slug
		foreach ($data as $key => & $item) {
			$item['url'] = url($prefix.'/'.$item['title']);
		}
		return $data;		
	}

    
   public function index(Request $request)
{ 
      // Retrieve the user's input and escape it 
        
	$query = e($request->get('q',''));

	
	if(!$query && $query == '') return Response::json(array(), 400);

	$products = Product::where('article','like','%'.$query.'%')
	->where('title','like','%'.$query.'%')
	->orderBy('title','asc')
	->take(5)
	->get(array('title','article','image'))->toArray();

	$categories = Categorie::where('title','like','%'.$query.'%')
	->has('products')
	->take(5)
	->get(array('title', 'article'))
	->toArray();

        $categories = $this->appendValue($categories, url('img/icons/category-icon.png'),'icon');
        $products 	= $this->appendURL($products, 'products');
		$categories  = $this->appendURL($categories, 'categories');
		// Add type of data to each item of each set of results
		$products = $this->appendValue($products, 'product', 'class');
		$categories = $this->appendValue($categories, 'categorie', 'class');

	// Add type of data to each item of each set of results
	// Merge all data into one array
	$data = array_merge($products, $categories);

	return Response::json(array(
		'data'=>$data
	));
}
}