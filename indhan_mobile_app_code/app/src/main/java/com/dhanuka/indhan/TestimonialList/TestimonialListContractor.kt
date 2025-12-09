package com.dhanuka.indhan.TestimonialList

import java.util.*

interface TestimonialListContractor {

    interface TestimonialListView {
        fun showProgress()

        fun hideProgress()

        fun setItems(items: TestimonialBean)

        fun failure(items: String)
    }


    interface TestimonialListPresenter {

        fun onGetTestimonialList(mMyVillage: HashMap<String, Any>)
    }

    interface TestimonialListInteractor {

        fun onTestimonialList(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onTestimonialListSuccess(items: TestimonialBean)

            fun onTestimonialListFailed(errorMessage: String)
        }
    }
}