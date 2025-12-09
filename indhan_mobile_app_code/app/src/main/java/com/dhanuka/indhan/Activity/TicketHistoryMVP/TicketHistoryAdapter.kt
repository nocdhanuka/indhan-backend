package com.dhanuka.indhan.Activity.TicketHistoryMVP


import android.content.Intent
import android.graphics.Color
import android.text.SpannableString
import android.text.style.UnderlineSpan
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Activity.TicketHistoryMVP.Model.TicketHistoryModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.validatefarmers.ValidateFarmersActivity
import kotlinx.android.synthetic.main.ticket_history_row.view.*


/**
 * Created by Shivam on 3/6/2018.
 */

class TicketHistoryAdapter(
    earningRecordResponseModelArrayList1: ArrayList<TicketHistoryModel.Datum>,
    private val mActivityContext: AppCompatActivity
) : RecyclerView.Adapter<TicketHistoryAdapter.MyViewHolder>() {

    private val filteredData: List<TicketHistoryModel.Datum>
    private lateinit var clickListener: TicketHistoryClickListener


    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

        init {
            view.setOnClickListener { view ->
                try {
                    clickListener.onClick(view, filteredData[adapterPosition])
                } catch (exp: ArrayIndexOutOfBoundsException) {
                }
            }
        }
    }

    fun setClickListener(itemClickListener: TicketHistoryClickListener) {
        this.clickListener = itemClickListener
    }

    init {
        this.filteredData = earningRecordResponseModelArrayList1
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): TicketHistoryAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.ticket_history_row, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: TicketHistoryAdapter.MyViewHolder, position: Int) {
        var value = ""

        val ticketHistoryBean = filteredData[position]
        holder.itemView.tvMeTicketNo.text = ticketHistoryBean.ticket
        holder.itemView.tvMeDate.text = ticketHistoryBean.datetimeFormat
        holder.itemView.tvMeDescription.text = ticketHistoryBean.description
        value = if (ticketHistoryBean.totalValidFarmers == 0) {
            "0"
        } else
            ticketHistoryBean.totalValidFarmers.toString()
        if (ticketHistoryBean.isOffline) {
            holder.itemView.offlineTicket.visibility = View.VISIBLE
        } else {
            holder.itemView.offlineTicket.visibility = View.GONE
        }
        holder.itemView.total_ifc_farmer.visibility = View.GONE
        val dataSource = ticketHistoryBean.dataSource ?: ""

        if (dataSource.equals("IFC", ignoreCase = true)) {
            holder.itemView.total_ifc_farmer.visibility = View.VISIBLE
            holder.itemView.total_farmer.visibility = View.GONE
            val content0 =
                SpannableString("Total IFC Farmers : " + ticketHistoryBean.ifcFarmerCount.toString())
            content0.setSpan(UnderlineSpan(), 0, content0.length, 0)
            holder.itemView.total_ifc_farmer.text = content0
        } else {
            holder.itemView.total_farmer.visibility = View.VISIBLE
        }

        val content = SpannableString("Total Validated Farmers : $value")
        content.setSpan(UnderlineSpan(), 0, content.length, 0)
        holder.itemView.total_valid_farmers.text = content

        val content1 = SpannableString("Total Farmers : " + ticketHistoryBean.totalFarmers)
        content1.setSpan(UnderlineSpan(), 0, content1.length, 0)
        holder.itemView.total_farmer.text = content1

        holder.itemView.total_ifc_farmer.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Total IFC Farmers")
            intent.putExtra("key", "ifc_farmer")
            intent.putExtra("ticket_number", filteredData[position].ticket)
            intent.putExtra("atpId", filteredData[position].tID.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        holder.itemView.total_valid_farmers.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Validate Farmers")
            intent.putExtra("key", "validate_farmer")
            intent.putExtra("ticket_number", filteredData[position].ticket)
            intent.putExtra("atpId", filteredData[position].tID.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        holder.itemView.total_farmer.setOnClickListener {
            val intent = Intent(mActivityContext, ValidateFarmersActivity::class.java)
            intent.putExtra("data", "Total Farmers")
            intent.putExtra("key", "total_farmer")
            intent.putExtra("ticket_number", filteredData[position].ticket)
            intent.putExtra("atpId", filteredData[position].tID.toString())
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP)
            mActivityContext.startActivity(intent)
        }

        if (ticketHistoryBean.ticketStatus.equals("ACTIVE", ignoreCase = true)) {
            holder.itemView.ivActive.visibility = View.VISIBLE
            holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#FF0CA160"))
            holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FFE5E3E6"))
            holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.offlineTicket.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.total_farmer.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.total_valid_farmers.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.total_ifc_farmer.setTextColor(Color.parseColor("#ffffff"))
            holder.itemView.ivArrow.setImageResource(R.drawable.forward)
        } else if (ticketHistoryBean.ticketStatus.equals("INACTIVE", ignoreCase = true)) {
            holder.itemView.ivActive.visibility = View.GONE
            holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#ffffff"))
            holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#FF0CA160"))
            holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FF7C7474"))
            holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.total_farmer.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.total_ifc_farmer.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.total_valid_farmers.setTextColor(Color.parseColor("#FF837B7B"))
            holder.itemView.ivArrow.setImageResource(R.drawable.rightarrow_grey)
        }


    }

    override fun getItemCount(): Int {
        return filteredData.size
    }
}
