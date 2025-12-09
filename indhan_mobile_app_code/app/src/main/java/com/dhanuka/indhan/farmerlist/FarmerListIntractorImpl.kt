package com.dhanuka.indhan.farmerlist

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class FarmerListIntractorImpl : FarmerListConractor.FarmerListInteractor {

    override fun onFarmerList(
        listener: FarmerListConractor.FarmerListInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getNewFarmerList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FarmerBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onFarmerListFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: FarmerBean) {
                    listener.onFarmerListSuccess(villageBean)
                }
            }
            )
    }
}