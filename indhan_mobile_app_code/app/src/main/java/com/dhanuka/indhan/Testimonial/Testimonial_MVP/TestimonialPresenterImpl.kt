package com.dhanuka.indhan.Testimonial.Testimonial_MVP

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Testimonial.Model.TestimonialResponse
import okhttp3.MultipartBody
import okhttp3.RequestBody

class TestimonialPresenterImpl(context: Context) : TestimonialFactory.TestimonialPresenter {
    override fun onSubmitPhoto(model: HashMap<String, RequestBody>, body: MultipartBody.Part) {
        listenr.showProgress()
        interactor.submitTestimonialData(this, model, body)
    }

    var listenr: TestimonialFactory.TestimonialHomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context as TestimonialFactory.TestimonialHomeListener
        interactor = SyncServiceInteractorImpl()
    }


    override fun onSuccessUpload(response: TestimonialResponse) {
        listenr.hideProgress()


        if (response.statusCode == 200) {
            listenr.onSuccess(response.message)
        } else {
            listenr.onFailure(response.message)
        }
    }

    override fun onFailure(token: String) {
        listenr.hideProgress()
        listenr.onFailure(token)
    }

}