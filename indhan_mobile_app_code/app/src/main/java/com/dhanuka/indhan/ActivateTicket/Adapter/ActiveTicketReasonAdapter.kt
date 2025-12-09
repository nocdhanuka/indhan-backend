package com.dhanuka.indhan.PlanSyllabusForBdo.Adapter

import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ActivateTicket.Model.AtpList
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.layout_atp_model.view.*

//

class ActiveTicketReasonAdapter(homeCategoryRecordResponseModelArrayList: ArrayList<AtpList>) :
    RecyclerView.Adapter<ActiveTicketReasonAdapter.MyViewHolder>() {

    private var filteredData: ArrayList<AtpList>
    var record_bck: ArrayList<AtpList>? = ArrayList()


    lateinit var onclickListener: ActiveTicketReasonAdapterClickListener

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

    fun setClickListener(onclickListener: ActiveTicketReasonAdapterClickListener) {

        this.onclickListener = onclickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): MyViewHolder {
        val itemView =
            LayoutInflater.from(parent.context).inflate(R.layout.layout_atp_model, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: MyViewHolder, position: Int) {
        val plan = filteredData[position]
        holder.itemView.product_name.text = plan.product
        holder.itemView.crop_name.text = plan.crop
        holder.itemView.address_tv.text = plan.tehsil + ", " + plan.district + ", " + plan.state
        holder.itemView.details.text =
            "Farmers - " + plan.expectedFarmer.toString() + " PDA - " + plan.expectedPda + " Meeting - " + plan.dataSource
        val dates = Utility.getDateForView(plan.planDate)

        if (position == 0) {
            holder.itemView.date.text = dates
            holder.itemView.date.visibility = View.VISIBLE
        } else {
            if (plan.planDate.equals(filteredData[position - 1].planDate)) {
                holder.itemView.date.visibility = View.GONE
            } else {
                holder.itemView.date.visibility = View.VISIBLE
                holder.itemView.date.text = dates
            }
        }
        if (plan.status == 0)
            holder.itemView.ivActive.visibility = View.GONE
        else
            holder.itemView.ivActive.visibility = View.VISIBLE
    }


    override fun getItemCount(): Int {
        return filteredData.size
    }

    interface ActiveTicketReasonAdapterClickListener {
        fun onSelectedPlanAtp(model: AtpList)
    }


}
