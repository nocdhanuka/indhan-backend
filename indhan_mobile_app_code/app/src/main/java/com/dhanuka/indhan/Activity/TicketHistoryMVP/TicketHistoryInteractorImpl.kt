package com.dhanuka.indhan.Activity.TicketHistoryMVP

import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.NetworkClientRx
import io.reactivex.Observer
import io.reactivex.disposables.Disposable
import io.reactivex.subjects.Subject

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketHistoryInteractorImpl : TicketHistoryContracts.TicketHistoryInteractor {
    private lateinit var apiClient: ApiServiceRequest
    override fun userTicketHistory(
        listener: TicketHistoryContracts.TicketHistoryInteractor.OnFinishedListener,
        token: String,
        tickerId: String,
        offset1: String
    ) {
        apiClient = NetworkClientRx.INSTANCE.api!!
        apiClient.userHistory(token, tickerId, offset1)
            .compose(NetworkClientRx.INSTANCE.IoMain())
            .subscribe(object : Subject<TicketHistoryModel>() {
                override fun hasObservers(): Boolean {
                    return false
                }

                override fun hasThrowable(): Boolean {
                    return false
                }

                override fun hasComplete(): Boolean {
                    return false
                }

                override fun getThrowable(): Throwable? {
                    return null
                }

                override fun subscribeActual(observer: Observer<in TicketHistoryModel>) {

                }

                override fun onSubscribe(d: Disposable) {

                }

                override fun onNext(ticketListBean: TicketHistoryModel) {
                    listener.onTicketHistorySucess(ticketListBean)
                }

                override fun onError(e: Throwable) {
                    listener.onTicketHistoryFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onComplete() {

                }
            })
    }
}
