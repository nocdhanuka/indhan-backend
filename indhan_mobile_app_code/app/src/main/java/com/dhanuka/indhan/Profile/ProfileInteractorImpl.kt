package com.dhanuka.indhan.Profile

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class ProfileInteractorImpl : ProfileContractor.ProfileInteractor {

    override fun ddProfile(
        listener: ProfileContractor.ProfileInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).ddprofilesummary(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DDProfileBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onProfileFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(loginBean: DDProfileBean) {
                    listener.onProfileSuccess(loginBean)
                }
            }
            )
    }
}