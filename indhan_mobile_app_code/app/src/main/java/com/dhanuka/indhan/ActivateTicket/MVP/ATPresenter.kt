package com.dhanuka.indhan.ActivateTicket.MVP

import android.content.Context
import com.dhanuka.indhan.ActivateTicket.Model.ActiveTicketListModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Preference.Preference

/**
 * Created by John on 5/31/2018.
 */
class ATPresenter(context: Context) : ActivateTicketPresenterFactory.ActivateTicketPresenter {


    val home: ActivateTicketPresenterFactory.ActivityView
    val interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    val preference: Preference

    init {
        this.interactor = SyncServiceInteractorImpl()
        preference = Preference(context)
        home = context as ActivateTicketPresenterFactory.ActivityView

    }


    override fun onRequest() {
        home.showProgress()
        interactor.getActiveTicketList(this, preference.getString("token"))
    }

    override fun onActivateTicketResponseSucess(model: ActiveTicketListModel) {
        home.hideProgress()
        when (model.statusCode) {
            200 -> {
                home.onActivateTicketResponseSucess(model)
            }
            409 -> {
                home.onActivateTicketResponseFailure("logout")
            }
            else -> {
                home.onActivateTicketResponseFailure("404")
            }
        }
    }

    override fun onActivateTicketResponseFailure(msg: String) {
        home.onActivateTicketResponseFailure(msg)

    }

    override fun onRequestPreviousData(token: String) {

    }

}