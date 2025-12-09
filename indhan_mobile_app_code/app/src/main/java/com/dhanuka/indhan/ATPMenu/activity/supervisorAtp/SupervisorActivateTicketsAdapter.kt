package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp


import android.content.Intent
import android.graphics.Color
import android.text.SpannableString
import android.text.style.UnderlineSpan
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.R
import com.dhanuka.indhan.validatefarmers.ValidateFarmersActivity
import kotlinx.android.synthetic.main.layout_atp_model_supervisor.view.*
import kotlinx.android.synthetic.main.ticket_history_row.view.ivActive
import kotlinx.android.synthetic.main.ticket_history_row.view.ivArrow
import kotlinx.android.synthetic.main.ticket_history_row.view.rlBackground
import kotlinx.android.synthetic.main.ticket_history_row.view.total_farmer
import kotlinx.android.synthetic.main.ticket_history_row.view.total_valid_farmers
import kotlinx.android.synthetic.main.ticket_history_row.view.tvMeDate
import kotlinx.android.synthetic.main.ticket_history_row.view.tvMeDescription
import kotlinx.android.synthetic.main.ticket_history_row.view.tvMeTicketNo
import java.util.*


class SupervisorActivateTicketsAdapter(
    earningRecordResponseModelArrayList1: ArrayList<SupervisorActivateBean.Data>,
    private val mActivityContext: AppCompatActivity,
    var clickListener: OnClickListenerArrivalDeparture
) : RecyclerView.Adapter<SupervisorActivateTicketsAdapter.MyViewHolder>() {

    private val filteredData: List<SupervisorActivateBean.Data>

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {}

    init {
        this.filteredData = earningRecordResponseModelArrayList1
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): SupervisorActivateTicketsAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_atp_model_supervisor, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(
        holder: SupervisorActivateTicketsAdapter.MyViewHolder,
        position: Int
    ) {
        var value = ""

        val ticketHistoryBean = filteredData[position]
        holder.itemView.tvMeTicketNo.text = ticketHistoryBean.ticket
        holder.itemView.tvMeDate.text = ticketHistoryBean.datetimeFormat
        holder.itemView.tvMeDescription.text = ticketHistoryBean.description
        value = if (ticketHistoryBean.totalValidFarmers == "0") {
            "0"
        } else
            ticketHistoryBean.totalValidFarmers

        val content = SpannableString("Total Validated Farmers : $value")
        content.setSpan(UnderlineSpan(), 0, content.length, 0)
        holder.itemView.total_valid_farmers.text = content

        val content1 = SpannableString("Total Farmers : " + ticketHistoryBean.totalFarmers)
        content1.setSpan(UnderlineSpan(), 0, content1.length, 0)
        holder.itemView.total_farmer.text = content1

        holder.itemView.total_valid_farmers.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("atpId", filteredData[position].tID.toString())

            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        if (filteredData[position].isArrivalDeparture == 2) {
            holder.itemView.txtDeparture.visibility = View.GONE
            holder.itemView.txtArrival.visibility = View.GONE
        } else {
            holder.itemView.txtDeparture.visibility = View.VISIBLE
            holder.itemView.txtArrival.visibility = View.VISIBLE

            if (filteredData[position].isArrivalDeparture == 0) {
                holder.itemView.txtArrival.isEnabled = true
                holder.itemView.txtDeparture.isEnabled = false
                holder.itemView.txtArrival.setTextColor(Color.parseColor("#FFFFFFFF"))
                holder.itemView.txtArrival.setBackgroundColor(Color.parseColor("#FF1AAF56"))
                holder.itemView.txtDeparture.setBackgroundColor(Color.parseColor("#FF9B9B9B"))
                holder.itemView.txtDeparture.setTextColor(Color.parseColor("#FFFFFFFF"))
            } else {
                holder.itemView.txtArrival.isEnabled = false
                holder.itemView.txtDeparture.isEnabled = true
                holder.itemView.txtArrival.setBackgroundColor(Color.parseColor("#FF9B9B9B"))
                holder.itemView.txtDeparture.setBackgroundColor(Color.parseColor("#FF1AAF56"))
                holder.itemView.txtArrival.setTextColor(Color.parseColor("#FFFFFFFF"))
                holder.itemView.txtDeparture.setTextColor(Color.parseColor("#FFFFFFFF"))
            }
        }

        holder.itemView.total_farmer.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Total Farmers")
            intent.putExtra("key", "total_farmer")
            intent.putExtra("atpId", filteredData[position].tID.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        holder.itemView.ivActive.visibility = View.GONE
        holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#ffffff"))
        holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#FF0CA160"))
        holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FF7C7474"))
        holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#FF837B7B"))
        holder.itemView.total_farmer.setTextColor(Color.parseColor("#FF837B7B"))
        holder.itemView.total_valid_farmers.setTextColor(Color.parseColor("#FF837B7B"))
        holder.itemView.ivArrow.setImageResource(R.drawable.rightarrow_grey)

        holder.itemView.setOnClickListener {
            val intent = Intent(mActivityContext, SupervisorDetailsActivity::class.java)
            intent.putExtra("fromActiveATP", filteredData[position])
            mActivityContext.startActivity(intent)
        }

        holder.itemView.txtArrival.setOnClickListener {
            clickListener.onClick(position, 1)
        }
        holder.itemView.txtDeparture.setOnClickListener {
            clickListener.onClick(position, 2)
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface OnClickListenerArrivalDeparture {
        fun onClick(position: Int, from: Int)
    }
}
