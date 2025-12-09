package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.Model.TicketList
import kotlinx.android.synthetic.main.layout_sp_ticket.view.*

//

class SelfParticipationAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<TicketList>) :
    RecyclerView.Adapter<SelfParticipationAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<TicketList>
    var record_bck: ArrayList<TicketList>? = ArrayList()

    lateinit var onclickListener: SelfParticipationClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
        this.record_bck?.addAll(homeCategoryRecordResponseModelArrayList)

    }


    fun setRecord_bck(record_bck: List<TicketList>) {
        this.record_bck?.clear()
        this.record_bck?.addAll(record_bck)
    }

    fun clear() {
        this.record_bck?.clear()
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onSelected(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: SelfParticipationClickListener) {

        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_sp_ticket, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.bdo_name.text = plan.name
        holder.itemView.address_tv.text = plan.ticket + "     |    " + plan.datetimeFormat
        holder.itemView.details.text = plan.product1 + ", " + plan.crop1 + ", " + plan.dataSource
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface SelfParticipationClickListener {
        fun onSelected(model: TicketList)
    }

    fun filter(charText: String) {
        var charText = charText
        filteredData.clear()
        if (charText.length == 0 || charText.equals("Select BDO")) {
            filteredData?.addAll(record_bck!!)
        } else {
            for (wp in record_bck!!) {
                if (!wp.name.isNullOrEmpty()) {
                    if (wp.name.equals(charText)) {
                        filteredData.add(wp)
                    }
                }
            }
        }
        notifyDataSetChanged()
    }

}
