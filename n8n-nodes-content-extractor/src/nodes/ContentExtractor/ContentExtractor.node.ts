import {
	IExecuteFunctions,
	INodeExecutionData,
	INodeType,
	INodeTypeDescription,
	NodeOperationError,
} from 'n8n-workflow';

export class ContentExtractor implements INodeType {
	description: INodeTypeDescription = {
		displayName: 'Content Extractor',
		name: 'contentExtractor',
		icon: 'file:contentExtractor.svg',
		group: ['transform'],
		version: 1,
		subtitle: '={{$parameter["operation"] + ": " + $parameter["resource"]}}',
		description: 'Extraire des transcriptions YouTube et scraper du contenu web',
		defaults: {
			name: 'Content Extractor',
		},
		inputs: ['main'],
		outputs: ['main'],
		credentials: [
			{
				name: 'contentExtractorApi',
				required: true,
			},
		],
		properties: [
			{
				displayName: 'Resource',
				name: 'resource',
				type: 'options',
				noDataExpression: true,
				options: [
					{
						name: 'YouTube',
						value: 'youtube',
						description: 'Extraire la transcription d\'une vidéo YouTube',
					},
					{
						name: 'Web Page',
						value: 'webpage',
						description: 'Scraper le contenu d\'une page web',
					},
				],
				default: 'youtube',
			},
			
			// YouTube Options
			{
				displayName: 'URL de la vidéo',
				name: 'videoUrl',
				type: 'string',
				default: '',
				required: true,
				displayOptions: {
					show: {
						resource: ['youtube'],
					},
				},
				description: 'L\'URL de la vidéo YouTube',
			},
			{
				displayName: 'Options',
				name: 'youtubeOptions',
				type: 'collection',
				placeholder: 'Ajouter une option',
				default: {},
				displayOptions: {
					show: {
						resource: ['youtube'],
					},
				},
				options: [
					{
						displayName: 'Langue',
						name: 'preferredLanguage',
						type: 'string',
						default: 'fr',
						description: 'Code de langue préféré (fr, en, es, etc.)',
					},
					{
						displayName: 'Inclure les timestamps',
						name: 'includeTimestamps',
						type: 'boolean',
						default: true,
						description: 'Inclure les marqueurs temporels dans la transcription',
					},
					{
						displayName: 'Segments à combiner',
						name: 'timestampsToCombine',
						type: 'number',
						default: 5,
						description: 'Nombre de segments de transcription à combiner',
					},
				],
			},
			
			// Web Scraping Options
			{
				displayName: 'URL',
				name: 'url',
				type: 'string',
				default: '',
				required: true,
				displayOptions: {
					show: {
						resource: ['webpage'],
					},
				},
				description: 'L\'URL de la page à scraper',
			},
			{
				displayName: 'Options',
				name: 'scrapingOptions',
				type: 'collection',
				placeholder: 'Ajouter une option',
				default: {},
				displayOptions: {
					show: {
						resource: ['webpage'],
					},
				},
				options: [
					{
						displayName: 'Format',
						name: 'format',
						type: 'options',
						options: [
							{
								name: 'Markdown',
								value: 'markdown',
							},
							{
								name: 'HTML',
								value: 'html',
							},
						],
						default: 'markdown',
						description: 'Format de sortie du contenu',
					},
					{
						displayName: 'Nettoyer le contenu',
						name: 'cleaned',
						type: 'boolean',
						default: true,
						description: 'Supprimer les éléments non essentiels (navigation, publicités, etc.)',
					},
					{
						displayName: 'JavaScript rendering',
						name: 'renderJs',
						type: 'boolean',
						default: false,
						description: 'Activer pour les sites nécessitant JavaScript (plus lent)',
					},
				],
			},
		],
	};

	async execute(this: IExecuteFunctions): Promise<INodeExecutionData[][]> {
		const items = this.getInputData();
		const returnData: INodeExecutionData[] = [];
		const resource = this.getNodeParameter('resource', 0) as string;
		const credentials = await this.getCredentials('contentExtractorApi');
		
		// Déterminer l'endpoint
		let baseUrl: string;
		let apiKey: string;
		
		if (credentials.endpoint === 'cloud') {
			baseUrl = 'https://api.automatehub.fr/content-extractor';
			apiKey = credentials.apiKey as string;
		} else {
			baseUrl = credentials.baseUrl as string;
			apiKey = credentials.selfApiKey as string;
		}
		
		// Nettoyer l'URL de base
		baseUrl = baseUrl.replace(/\/$/, '');
		
		for (let itemIndex = 0; itemIndex < items.length; itemIndex++) {
			try {
				let endpoint: string;
				let body: any = {};
				
				if (resource === 'youtube') {
					endpoint = `${baseUrl}/api/v1/get-youtube-transcript`;
					body.videoUrl = this.getNodeParameter('videoUrl', itemIndex) as string;
					
					const options = this.getNodeParameter('youtubeOptions', itemIndex, {}) as any;
					if (options.preferredLanguage) body.preferredLanguage = options.preferredLanguage;
					if (options.includeTimestamps !== undefined) body.includeTimestamps = options.includeTimestamps;
					if (options.timestampsToCombine) body.timestampsToCombine = options.timestampsToCombine;
					
				} else if (resource === 'webpage') {
					endpoint = `${baseUrl}/api/v1/scrape`;
					body.url = this.getNodeParameter('url', itemIndex) as string;
					
					const options = this.getNodeParameter('scrapingOptions', itemIndex, {}) as any;
					if (options.format) body.format = options.format;
					if (options.cleaned !== undefined) body.cleaned = options.cleaned;
					if (options.renderJs !== undefined) body.renderJs = options.renderJs;
				}
				
				const response = await this.helpers.httpRequest({
					method: 'POST',
					url: endpoint!,
					headers: {
						'Authorization': `Bearer ${apiKey}`,
						'Content-Type': 'application/json',
					},
					body,
					json: true,
				});
				
				if (response.success) {
					returnData.push({
						json: response,
						pairedItem: { item: itemIndex },
					});
				} else {
					throw new NodeOperationError(
						this.getNode(),
						`Erreur: ${response.error || 'Extraction échouée'}`,
						{ itemIndex }
					);
				}
				
			} catch (error) {
				if (this.continueOnFail()) {
					returnData.push({
						json: {
							error: error.message,
						},
						pairedItem: { item: itemIndex },
					});
					continue;
				}
				throw error;
			}
		}
		
		return [returnData];
	}
}