@extends('layouts.back-end.common_seller_1')


@section('content')
<style>
  .empty-state {
    text-align: center;
    padding: 40px 20px;
    max-width: 600px;
    margin: 60px auto;
    color: #444;
    font-family: Arial, sans-serif;
  }

  .empty-image {
    width: 120px;
    margin-bottom: 20px;
    margin: auto;
  }

  .empty-state h2 {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 12px;
  }

  .empty-state p {
    font-size: 16px;
    color: #777;
    margin-bottom: 20px;
  }

  .empty-state p strong {
    color: #444;
  }

  .explore-link {
    color: #0056b3;
    text-decoration: none;
    font-weight: 600;
  }

  .explore-link:hover {
    text-decoration: underline;
  }
</style>




<main class="main">
  <div class="empty-state">
    <img src="{{ asset('public/website/assets/images/product-not-found.webp') }}" alt="Empty box" class="empty-image" />
    <h2>Oops! Product not found.</h2>
    <p>
      List will be <strong>updated soon</strong>, or the product is <strong>Not Available</strong> at the moment.
    </p>
    <a href="{{ url('/') }}">Explore more products</a>
  </div>

</main>
@endsection