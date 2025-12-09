package com.dhanuka.indhan.Testimonial.Testimonial_MVP

import com.dhanuka.indhan.Testimonial.Model.TestimonialResponse
import okhttp3.MultipartBody
import okhttp3.RequestBody

interface TestimonialFactory {
    interface TestimonialHomeListener {
        fun showProgress()
        fun hideProgress()
        fun onSuccess(model: String)
        fun onFailure(mes: String)
    }

    interface TestimonialPresenter {
        fun onFailure(token: String)
        fun onSubmitPhoto(model: HashMap<String, RequestBody>, body: MultipartBody.Part)
        fun onSuccessUpload(response: TestimonialResponse)
    }


}