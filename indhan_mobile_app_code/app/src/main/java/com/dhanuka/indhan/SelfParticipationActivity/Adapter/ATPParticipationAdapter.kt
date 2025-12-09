package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.Model.AtpList
import kotlinx.android.synthetic.main.layout_sp_ticket.view.*

//

class ATPParticipationAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<AtpList>) :
    RecyclerView.Adapter<ATPParticipationAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<AtpList>
    var record_bck: ArrayList<AtpList>? = ArrayList()


    lateinit var onclickListener: ATPParticipationAdapterClickListener

    init {
        this.filteredData = homeCategoryRecordResponseModelArrayList
        this.record_bck?.addAll(homeCategoryRecordResponseModelArrayList)

    }


    fun setRecord_bck(record_bck: List<AtpList>) {
        this.record_bck?.clear()
        this.record_bck?.addAll(record_bck)
    }

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            view.setOnClickListener { view -> onclickListener.onSelectedPlanAtp(filteredData[adapterPosition]) }
        }
    }

    fun setClickListener(onclickListener: ATPParticipationAdapterClickListener) {

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
        holder.itemView.address_tv.text = plan.datetimeFormat
        holder.itemView.details.text = plan.product1 + ", " + plan.crop1 + ", " + plan.dataSource
        /*if (plan.planAtpStatus == 1) {
            holder.itemView.status_tv.visibility=View.VISIBLE
            holder.itemView.status_tv.text = "Already Planned"
            holder.itemView.status_tv.setTextColor(ContextCompat.getColor(mContext, R.color.colorPrimary))
        } else {
            if (plan.valid_status <= 0) {
                holder.itemView.status_tv.visibility=View.VISIBLE
                holder.itemView.status_tv.text = "Invalid"
                holder.itemView.status_tv.setTextColor(Color.RED)
            } else if (plan.valid_status > 0) {
                holder.itemView.status_tv.visibility=View.GONE
                holder.itemView.status_tv.setTextColor(ContextCompat.getColor(mContext, R.color.colorPrimary))
            }
        }*/
    }


    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface ATPParticipationAdapterClickListener {
        fun onSelectedPlanAtp(model: AtpList)
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
