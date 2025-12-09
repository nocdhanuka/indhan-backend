package com.dhanuka.indhan.ActivateTicket.MVP

import android.content.Context
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Preference.Preference
import java.util.*

/**
 * Created by John on 5/31/2018.
 */
class TicketDetailSubmitPresenterImpl(context: Context) :
    ActivateTicketPresenterFactory.TicketDetailPresenter {


    override fun onActivateTicketSubmitResponseFailure(model: String) {
        detailhome.onActivateTicketResponseFailure(model)
    }

    override fun onActivateTicketSubmitResponseSucess(model: SubmitTicketBean) {
        detailhome.hideProgress()
        if (model.status_code == 200) {
            detailhome.onActivateTicketResponseSucess(model)
        } else if (model.status_code == 409) {
            detailhome.onActivateTicketResponseFailure("logout")
        }
    }

    override fun onActivateTicket(token: HashMap<String, Any>) {
        detailhome.showProgress()
        interactor.activateTicket(this, token)
    }

    val detailhome: ActivateTicketPresenterFactory.TicketDetailActivityView
    val interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    val preference: Preference

    init {
        this.interactor = SyncServiceInteractorImpl()
        preference = Preference(context)
        detailhome = context as ActivateTicketPresenterFactory.TicketDetailActivityView

    }


}