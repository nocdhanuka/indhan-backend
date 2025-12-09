package com.dhanuka.indhan.Activity.TicketDetailMVP

import com.dhanuka.indhan.Activity.TicketDetailMVP.Model.TicketDetailBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketDetailInteractorImpl : TicketDetailContracts.TicketDetailInteractor {

    override fun userTicketDetail(
        listener: TicketDetailContracts.TicketDetailInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .detailTicket(model).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TicketDetailBean>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onTicketDetailFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(ticketDetailBean: TicketDetailBean) {
                    listener.onTicketDetailSucess(ticketDetailBean)
                }
            })
    }
}
