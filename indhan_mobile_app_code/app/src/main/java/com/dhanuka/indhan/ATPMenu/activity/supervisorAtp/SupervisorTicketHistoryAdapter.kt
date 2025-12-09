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
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryAtpResponse
import com.dhanuka.indhan.R
import com.dhanuka.indhan.validatefarmers.ValidateFarmersActivity
import kotlinx.android.synthetic.main.ticket_atp_history_supervisor.view.*
import kotlinx.android.synthetic.main.ticket_history_row.view.ivActive
import kotlinx.android.synthetic.main.ticket_history_row.view.ivArrow
import kotlinx.android.synthetic.main.ticket_history_row.view.rlBackground
import kotlinx.android.synthetic.main.ticket_history_row.view.total_farmer
import kotlinx.android.synthetic.main.ticket_history_row.view.total_valid_farmers
import kotlinx.android.synthetic.main.ticket_history_row.view.tvMeDate
import kotlinx.android.synthetic.main.ticket_history_row.view.tvMeDescription
import kotlinx.android.synthetic.main.ticket_history_row.view.tvMeTicketNo
import java.io.Serializable
import java.util.*
import kotlin.math.roundToInt


/**
 * Created by Shivam on 3/6/2018.
 */

class SupervisorTicketHistoryAdapter(
    earningRecordResponseModelArrayList1: ArrayList<TicketHistoryAtpResponse.Data>,
    private val mActivityContext: AppCompatActivity
) : RecyclerView.Adapter<SupervisorTicketHistoryAdapter.MyViewHolder>() {

    private val filteredData: List<TicketHistoryAtpResponse.Data>

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    init {
        this.filteredData = earningRecordResponseModelArrayList1
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): SupervisorTicketHistoryAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.ticket_atp_history_supervisor, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(
        holder: SupervisorTicketHistoryAdapter.MyViewHolder,
        position: Int
    ) {
        var value = ""

        val ticketHistoryBean = filteredData[position]
        holder.itemView.tvMeTicketNo.text = ticketHistoryBean.ticket
        holder.itemView.tvMeDate.text = ticketHistoryBean.datetime_format
        holder.itemView.tvMeDescription.text = ticketHistoryBean.description
        if (ticketHistoryBean.total_valid_farmers == 0) {
            value = "0"
        } else
            value = ticketHistoryBean.total_valid_farmers.toString()

        val content = SpannableString("Total Validated Farmers : $value")
        content.setSpan(UnderlineSpan(), 0, content.length, 0)
        holder.itemView.total_valid_farmers.text = content

        val content1 = SpannableString("Total Farmers : " + ticketHistoryBean.total_farmers)
        content1.setSpan(UnderlineSpan(), 0, content1.length, 0)
        holder.itemView.total_farmer.text = content1

        if (ticketHistoryBean.duration != null) {
            holder.itemView.txtActivityDuration.visibility = View.VISIBLE
            var duration = ""
            duration = if (ticketHistoryBean.duration.contains(".")) {
                "- ${ticketHistoryBean.duration.split(".")[0].toInt()} hours " +
                        "0.${ticketHistoryBean.duration.split(".")[1]}".toDouble().times(60)
                            .toInt() + " minutes"
            } else {
                "- ${(ticketHistoryBean.duration.toDouble().roundToInt())} hours"
            }
            holder.itemView.txtActivityDuration.text = "Activity Duration : $duration"
        } else {
            holder.itemView.txtActivityDuration.visibility = View.GONE
        }

        holder.itemView.total_valid_farmers.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("atpId", filteredData[position].tid.toString())

            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        holder.itemView.total_farmer.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Total Farmers")
            intent.putExtra("key", "total_farmer")
            intent.putExtra("atpId", filteredData[position].tid.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        if (ticketHistoryBean.ticket_status.equals("ACTIVE", ignoreCase = true)) {
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.txtHighLight.setTextColor(mActivityContext.resources.getColor(R.color.white))
            holder.itemView.txtActivityDuration.setTextColor(mActivityContext.resources.getColor(R.color.white))
            holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#FF0CA160"))
            holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FFE5E3E6"))
            holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.total_farmer.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.total_valid_farmers.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.ivArrow.setImageResource(R.drawable.forward)
        } else if (ticketHistoryBean.ticket_status.equals("INACTIVE", ignoreCase = true)) {
            holder.itemView.ivActive.visibility = View.GONE
            holder.itemView.txtHighLight.setTextColor(mActivityContext.resources.getColor(R.color.colorButton))
            holder.itemView.txtActivityDuration.setTextColor(mActivityContext.resources.getColor(R.color.colorButton))
            holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#ffffff"))
            holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#FF0CA160"))
            holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FF7C7474"))
            holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.total_farmer.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.total_valid_farmers.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.ivArrow.setImageResource(R.drawable.rightarrow_grey)
        }

        holder.itemView.setOnClickListener {
            val intent = Intent(mActivityContext, SupervisorDetailsActivity::class.java)
            intent.putExtra("fromHistoryATP", filteredData[position] as Serializable)
            mActivityContext.startActivity(intent)
        }

        holder.itemView.txtHighLight.setOnClickListener {
            val intent = Intent(mActivityContext, HightLightActivity::class.java)
            intent.putExtra("ticketId", filteredData[position].tid.toString())
            mActivityContext.startActivity(intent)
        }


        holder.itemView.txtHighLight.text = "HIGHLIGHT : ${ticketHistoryBean.heightLightCount}"


    }

    override fun getItemCount(): Int {
        return filteredData.size
    }
}
