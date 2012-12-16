function openModalPanel(id)
{
	var detailModalPanelBackground=document.createElement("div");
	detailModalPanelBackground.id="zoomScroll"; 
	detailModalPanelBackground.className="visible loaded";
	//detailModalPanelBackground.classList.add("visible");
	//detailModalPanelBackground.classList.add("loaded");
	detailModalPanelBackground.addEventListener("click",closeModalPanel,false);
	
	var detailModalPanel=document.createElement("div");
	detailModalPanel.id="zoom";
	
	var someText="";
	for(var i=0;i<100;i++)
	{
		someText=someText+"<p>Sometext "+i+"</p>";
	}
	detailModalPanel.innerHTML=someText;
	detailModalPanelBackground.appendChild(detailModalPanel);
	
	
	document.body.style.overflow="hidden";
	document.body.appendChild(detailModalPanelBackground);
	return false;
}


function closeModalPanel()
{
	var detailModalPanelBackground=document.getElementById("zoomScroll");
	if(detailModalPanelBackground)
		detailModalPanelBackground.parentNode.removeChild(detailModalPanelBackground);
	document.body.style.overflow="scroll";
}