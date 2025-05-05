@extends('layouts.app')

@section('title', 'FAQ')

@section('page-css')
<style>
  .faq-header {
    position: relative;
    background-color: rgba(105, 108, 255, 0.16) !important;
    padding: 2rem;
    border-radius: 0.5rem;
  }
  .faq-header:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url("{{ asset('assets/img/illustrations/bg-shape-image-light.png') }}");
    background-repeat: no-repeat;
    background-size: cover;
    border-radius: 0.5rem;
  }
  .faq-image {
    position: absolute;
    right: 3rem;
    bottom: 0;
    max-width: 15rem;
  }
  .faq-search {
    background-color: #fff;
    border-radius: 2rem;
    padding-right: 1rem;
  }
  .faq-search .form-control {
    border: none;
    border-radius: 2rem;
  }
  .faq-search .form-control:focus {
    box-shadow: none;
  }
  .faq-category-icon {
    font-size: 1.625rem;
    margin-bottom: 0.5rem;
  }
  .card-faq {
    box-shadow: 0 0.25rem 1.125rem rgba(75, 70, 92, 0.1);
    transition: all 0.3s ease-in-out;
  }
  .card-faq:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(75, 70, 92, 0.2);
  }
  .faq-section-title {
    font-size: 1.125rem;
    margin-bottom: 1.25rem;
  }
  .faq-contact {
    background-color: #f7f7f9;
    border-radius: 0.5rem;
    padding: 2rem;
    text-align: center;
  }
  .faq-contact-icon {
    height: 3rem;
    width: 3rem;
    background-color: #e7e7ff;
    color: #696cff;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
  }
  .accordion-button:not(.collapsed) {
    color: #696cff;
    background-color: #f8f8f8;
    box-shadow: none;
  }
  .accordion-button:focus {
    box-shadow: none;
    border-color: #f0f0f0;
  }
</style>
@endsection

@section('content')
<!-- FAQ Header -->
<div class="faq-header d-flex justify-content-center position-relative mb-4">
  <div class="position-relative">
    <h3 class="text-center mb-1">Hello, how can we help?</h3>
    <p class="text-center mb-4">or choose a category to quickly find the help you need</p>
    <div class="input-group input-group-merge faq-search">
      <span class="input-group-text border-0" id="faq-search-icon">
        <i class="bx bx-search"></i>
      </span>
      <input type="text" class="form-control" placeholder="Search articles..." aria-label="Search articles..." aria-describedby="faq-search-icon">
    </div>
  </div>
  <img src="{{ asset('assets/img/illustrations/faq-header.png') }}" class="faq-image d-none d-md-block" alt="FAQ Image">
</div>

<!-- FAQ Categories -->
<div class="row mb-4">
  <div class="col-md-2 col-sm-12 mb-3">
    <div class="card card-faq text-center h-100">
      <div class="card-body">
        <div class="faq-category-icon text-primary">
          <i class='bx bx-credit-card'></i>
        </div>
        <h6>Payment</h6>
        <small class="text-muted">Get help with payment</small>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-12 mb-3">
    <div class="card card-faq text-center h-100">
      <div class="card-body">
        <div class="faq-category-icon text-primary">
          <i class='bx bx-package'></i>
        </div>
        <h6>Delivery</h6>
        <small class="text-muted">Get help with delivery</small>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-12 mb-3">
    <div class="card card-faq text-center h-100">
      <div class="card-body">
        <div class="faq-category-icon text-primary">
          <i class='bx bx-revision'></i>
        </div>
        <h6>Cancellation & Return</h6>
        <small class="text-muted">Get help with return</small>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-12 mb-3">
    <div class="card card-faq text-center h-100">
      <div class="card-body">
        <div class="faq-category-icon text-primary">
          <i class='bx bx-purchase-tag'></i>
        </div>
        <h6>My Orders</h6>
        <small class="text-muted">Order details</small>
      </div>
    </div>
  </div>
  <div class="col-md-2 col-sm-12 mb-3">
    <div class="card card-faq text-center h-100">
      <div class="card-body">
        <div class="faq-category-icon text-primary">
          <i class='bx bx-cube'></i>
        </div>
        <h6>Product & Services</h6>
        <small class="text-muted">Get help with product</small>
      </div>
    </div>
  </div>
</div>

<!-- Payment FAQ -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="faq-section-title">
          <i class="bx bx-credit-card text-primary me-2"></i>
          Payment
        </h5>
        <div class="accordion accordion-flush" id="accordionPayment">
          <div class="accordion-item">
            <h2 class="accordion-header" id="paymentOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaymentOne" aria-expanded="false" aria-controls="collapsePaymentOne">
                When is payment taken for my order?
              </button>
            </h2>
            <div id="collapsePaymentOne" class="accordion-collapse collapse" aria-labelledby="paymentOne" data-bs-parent="#accordionPayment">
              <div class="accordion-body">
                Payment is taken during the checkout process when you pay for your order. The order number that appears on the confirmation screen indicates payment has been successfully processed.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="paymentTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaymentTwo" aria-expanded="false" aria-controls="collapsePaymentTwo">
                How do I pay for my order?
              </button>
            </h2>
            <div id="collapsePaymentTwo" class="accordion-collapse collapse" aria-labelledby="paymentTwo" data-bs-parent="#accordionPayment">
              <div class="accordion-body">
                We accept Visa®, MasterCard®, American Express®, and PayPal®. Our servers encrypt all information submitted to them, so you can be confident that your credit card information will be kept safe and secure.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="paymentThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaymentThree" aria-expanded="false" aria-controls="collapsePaymentThree">
                What should I do if I'm having trouble placing an order?
              </button>
            </h2>
            <div id="collapsePaymentThree" class="accordion-collapse collapse" aria-labelledby="paymentThree" data-bs-parent="#accordionPayment">
              <div class="accordion-body">
                For any technical difficulties you are experiencing with our website, please contact us at our support portal, or you can call us toll-free at 1-000-000-000, or email us at order@companymail.com
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="paymentFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaymentFour" aria-expanded="false" aria-controls="collapsePaymentFour">
                Which license do I need for an end product that is only accessible to paying users?
              </button>
            </h2>
            <div id="collapsePaymentFour" class="accordion-collapse collapse" aria-labelledby="paymentFour" data-bs-parent="#accordionPayment">
              <div class="accordion-body">
                If you have paying users or you are developing any SaaS products then you need an Extended License. For each products, you need a license. You can get free lifetime updates as well.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="paymentFive">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaymentFive" aria-expanded="false" aria-controls="collapsePaymentFive">
                Does my subscription automatically renew?
              </button>
            </h2>
            <div id="collapsePaymentFive" class="accordion-collapse collapse" aria-labelledby="paymentFive" data-bs-parent="#accordionPayment">
              <div class="accordion-body">
                No, This is not subscription based item. Pastry pudding cookie toffee bonbon jujubes jujubes powder topping. Jelly beans gummi bears sweet roll bonbon muffin liquorice. Wafer lollipop sesame snaps.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Delivery FAQ -->
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="faq-section-title">
          <i class="bx bx-package text-primary me-2"></i>
          Delivery
        </h5>
        <div class="accordion accordion-flush" id="accordionDelivery">
          <div class="accordion-item">
            <h2 class="accordion-header" id="deliveryOne">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeliveryOne" aria-expanded="false" aria-controls="collapseDeliveryOne">
                How would you ship my order?
              </button>
            </h2>
            <div id="collapseDeliveryOne" class="accordion-collapse collapse" aria-labelledby="deliveryOne" data-bs-parent="#accordionDelivery">
              <div class="accordion-body">
                For large products, we deliver your product via a third party logistics company offering you the "room of choice" scheduled delivery service. For small products, we offer free parcel delivery.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="deliveryTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeliveryTwo" aria-expanded="false" aria-controls="collapseDeliveryTwo">
                What is the delivery cost of my order?
              </button>
            </h2>
            <div id="collapseDeliveryTwo" class="accordion-collapse collapse" aria-labelledby="deliveryTwo" data-bs-parent="#accordionDelivery">
              <div class="accordion-body">
                The cost of scheduled delivery is $69 or $99 per order, depending on the destination postal code. The parcel delivery is free.
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="deliveryThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDeliveryThree" aria-expanded="false" aria-controls="collapseDeliveryThree">
                What to do if my product arrives damaged?
              </button>
            </h2>
            <div id="collapseDeliveryThree" class="accordion-collapse collapse" aria-labelledby="deliveryThree" data-bs-parent="#accordionDelivery">
              <div class="accordion-body">
                We will promptly replace any product that is damaged in transit. Just contact our support team, to notify us of the situation within 48 hours of product arrival.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Still have questions section -->
<div class="row mb-4">
  <div class="col-12 text-center">
    <div class="card">
      <div class="card-body">
        <h4 class="mb-2">You still have a question?</h4>
        <p class="mb-4">If you can't find question in our FAQ, you can contact us. We'll answer you shortly!</p>
        
        <div class="row">
          <div class="col-sm-6">
            <div class="faq-contact mb-3">
              <div class="faq-contact-icon">
                <i class="bx bx-phone"></i>
              </div>
              <h5>+(9821) 2289 1616</h5>
              <p class="mb-0">We are always happy to help</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="faq-contact">
              <div class="faq-contact-icon">
                <i class="bx bx-envelope"></i>
              </div>
              <h5>help@mailzila.com</h5>
              <p class="mb-0">Best way to get a quick answer</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-js')
<script>
  // Initialize any needed JS for the FAQ page here
</script>
@endsection 