package com.dhanuka.indhan.Activity.NewTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.Model.NewTicketBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient

import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

/**
 * Created by Shivam on 2/28/2018.
 */

class NewTicketInteractorImpl : NewTicketContracts.NewTicketInteractor {

    override fun userNewTicket(
        listener: NewTicketContracts.NewTicketInteractor.OnFinishedListener,
        model: String
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).userNewTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<NewTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onNewTicketFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(newTicketBean: NewTicketBean) {
                    listener.onNewTicketSucess(newTicketBean)
                }
            })
    }
}
