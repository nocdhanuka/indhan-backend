package com.dhanuka.indhan.acpfarmers

import android.content.Context
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.item_acp_lead_history.view.*
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*


class AcpLeadAdapter(context: Context, ddList: ArrayList<ACPFarmerBean.Data.ACPDetail.Leads>) :
    RecyclerView.Adapter<AcpLeadAdapter.ViewHolder>() {
    var context: Context
    var ddList: ArrayList<ACPFarmerBean.Data.ACPDetail.Leads>

    init {
        this.context = context
        this.ddList = ddList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view: View =
            LayoutInflater.from(context).inflate(R.layout.item_acp_lead_history, parent, false)
        return ViewHolder(view)
    }

    override fun getItemCount(): Int {
        return ddList.size
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.view.txtLeadId.text = ddList.get(position).leadId

        holder.view.txtCreatedAt.text = getTime(ddList.get(position).createdAt)
        // holder.view.t

    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        var view: View

        init {
            this.view = view
        }
    }

    internal fun getTime(date: String): String {
        val finalTime = date
        var date1: Date? = null
        val sdf = SimpleDateFormat("yyyy-MM-dd HH:mm:ss")
        try {
            date1 = sdf.parse(date)
        } catch (e: ParseException) {
            e.printStackTrace()
        }
        val timeStr: String
        val inputFormat = SimpleDateFormat("dd-MMM-yyyy")//dd-MMM-yyyy  HH:mm a
        timeStr = inputFormat.format(date1)
        return timeStr
    }
}