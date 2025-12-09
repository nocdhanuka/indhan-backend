package com.dhanuka.indhan.Activity.RegisterFarmersMVP

import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.RegisterFarmersBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient

import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by Shivam on 3/8/2018.
 */

class RegisterFarmersInteractorImpl : RegisterFarmersContracts.RegisterFarmersInteractor {

    override fun userRegisterFarmers(
        listener: RegisterFarmersContracts.RegisterFarmersInteractor.OnFinishedListener,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).farmerTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<RegisterFarmersBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onRegisterFarmersFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(registerFarmersBean: RegisterFarmersBean) {
                    listener.onRegisterFarmersSucess(registerFarmersBean)
                }
            })
    }
}
