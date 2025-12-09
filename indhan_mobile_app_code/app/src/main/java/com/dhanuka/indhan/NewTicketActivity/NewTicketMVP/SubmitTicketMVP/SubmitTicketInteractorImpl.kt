package com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class SubmitTicketInteractorImpl : SubmitTicketContracts.SubmitTicketInteractor {
    override fun villageList(
        listener: SubmitTicketContracts.SubmitTicketInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).allVillageList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<MyVillageBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onSubmitTicketFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: MyVillageBean) {
                    listener.onVillageList(villageBean)
                }
            }
            )
    }


    override fun userSubmitTicket(
        listener: SubmitTicketContracts.SubmitTicketInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).submitTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SubmitTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onSubmitTicketFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(submitTicketBean: SubmitTicketBean) {
                    listener.onSubmitTicketSucess(submitTicketBean)
                }
            })
    }
}
