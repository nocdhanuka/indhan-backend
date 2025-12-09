package com.dhanuka.indhan.Activity.TicketHistoryMVP

import android.view.View
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel

/**
 * Created by Shivam on 3/6/2018.
 */

interface TicketHistoryClickListener {
    fun onClick(view: View, ticketHistoryBean: TicketHistoryModel.Datum)

}
