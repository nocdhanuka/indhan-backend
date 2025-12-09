package com.dhanuka.indhan.Activity.HomeMVP

import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient

import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by Shivam on 2/27/2018.
 */

class HomeInteractorImpl : HomeContracts.HomeInteractor {

    override fun userHome(
        listener: HomeContracts.HomeInteractor.OnFinishedListener,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userMenu(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<HomeBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onHomeFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(homeBean: HomeBean) {
                    listener.onHomeSucess(homeBean)
                }
            })
    }
}
