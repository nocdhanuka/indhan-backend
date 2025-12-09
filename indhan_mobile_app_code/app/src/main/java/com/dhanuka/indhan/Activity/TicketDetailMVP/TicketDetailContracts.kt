package com.dhanuka.indhan.Activity.TicketDetailMVP

import com.dhanuka.indhan.Activity.TicketDetailMVP.Model.TicketDetailBean
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

interface TicketDetailContracts {

    interface ITicketDetailView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: TicketDetailBean)
    }

    interface TicketDetailPresenter {

        fun onSubmitClick(mTicketDetailRequest: HashMap<String, Any>)

    }

    interface TicketDetailInteractor {

        fun userTicketDetail(
            listener: TicketDetailContracts.TicketDetailInteractor.OnFinishedListener,
            model: HashMap<String, Any>
        )

        interface OnFinishedListener {
            fun onTicketDetailSucess(items: TicketDetailBean)

            fun onTicketDetailFailed(errorMessage: String)
        }
    }
}
