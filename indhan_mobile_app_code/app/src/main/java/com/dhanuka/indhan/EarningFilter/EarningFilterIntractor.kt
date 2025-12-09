package com.dhanuka.indhan.EarningFilter

import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class EarningFilterIntractor : EarningFilterContractor.EarningFilterInteractor {
    override fun onVillageList(
        listener: EarningFilterContractor.EarningFilterInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).villageList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MyVillageBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onVillageFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: MyVillageBean) {
                    listener.onVillageSuccess(villageBean)
                }
            }
            )
    }
}