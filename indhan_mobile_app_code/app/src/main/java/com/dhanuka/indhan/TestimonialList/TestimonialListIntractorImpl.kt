package com.dhanuka.indhan.TestimonialList

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class TestimonialListIntractorImpl : TestimonialListContractor.TestimonialListInteractor {

    override fun onTestimonialList(
        listener: TestimonialListContractor.TestimonialListInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).TestimonialList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TestimonialBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onTestimonialListFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: TestimonialBean) {
                    when (villageBean.statusCode) {
                        409 -> {
                            EventBus.getDefault().post("logout")
                        }
                        200 -> {
                            listener.onTestimonialListSuccess(villageBean)
                        }
                        else -> {
                            listener.onTestimonialListFailed(villageBean.message)
                        }
                    }

                }
            }
            )
    }
}