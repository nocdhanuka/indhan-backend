package com.dhanuka.indhan.Activity.LogoutMVP

import com.dhanuka.indhan.Activity.LogoutMVP.Model.LogoutBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient

import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by Shivam on 2/27/2018.
 */

class LogoutInteractorImpl : LogoutContracts.LogoutInteractor {

    override fun userLogout(
        listener: LogoutContracts.LogoutInteractor.OnFinishedListener,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userLogout(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<LogoutBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onLogoutFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(logoutBean: LogoutBean) {
                    listener.onLogoutSucess(logoutBean)
                }
            })
    }
}
