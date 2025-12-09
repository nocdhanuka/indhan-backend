package com.dhanuka.indhan.TestimonialList

import java.util.*

class TestimonialListPresenterImpl(var view: TestimonialListContractor.TestimonialListView) :
    TestimonialListContractor.TestimonialListPresenter,
    TestimonialListContractor.TestimonialListInteractor.OnFinishedListener {

    private var testimonialListIntractorImpl: TestimonialListContractor.TestimonialListInteractor

    init {
        testimonialListIntractorImpl =
            com.dhanuka.indhan.TestimonialList.TestimonialListIntractorImpl()
    }

    override fun onGetTestimonialList(mMyVillage: HashMap<String, Any>) {
        view.showProgress()
        testimonialListIntractorImpl.onTestimonialList(this, mMyVillage)
    }

    override fun onTestimonialListSuccess(items: TestimonialBean) {
        view.hideProgress()
        view.setItems(items)

    }

    override fun onTestimonialListFailed(errorMessage: String) {
        view.hideProgress()
        view.failure(errorMessage)

    }
}