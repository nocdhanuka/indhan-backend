package com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP


import com.dhanuka.indhan.Activity.TicketDetailMVP.EndTicketMVP.Model.EndTicketBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/7/2018.
 */

class EndTicketInteractorImpl : EndTicketContracts.EndTicketInteractor {

    override fun userEndTicket(
        listener: EndTicketContracts.EndTicketInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).endTicket(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<EndTicketBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onEndTicketFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(endTicketBean: EndTicketBean) {
                    listener.onEndTicketSucess(endTicketBean)
                }
            })
    }
}
