package com.dhanuka.indhan.MyVillage


import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class MyVillageInteractor : MyVillageContractor.MyVillageInteractor {
    override fun ddMyVillage(
        listener: MyVillageContractor.MyVillageInteractor.OnFinishedListener,
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
                    listener.onVillageFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: MyVillageBean) {
                    if (villageBean != null) {
                        when (villageBean.statusCode) {
                            200 -> {
                                listener.onVillageSuccess(villageBean)
                            }
                            409 -> {
                                EventBus.getDefault().post("logout")
                            }
                            else -> {
                                listener.onVillageFailed(villageBean.message)
                            }
                        }
                    }
                }
            })
    }
}